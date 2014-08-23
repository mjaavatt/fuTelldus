<script src="../lib/packages/Highcharts-4.0.3/js/highcharts.js"></script>
<script src="../lib/packages/Highcharts-4.0.3/js/modules/exporting.js"></script>

<div class="container">
<?php

/* Get/set parameters
--------------------------------------------------------------------------- */
if (isset($_GET['id'])) {
	$getID = clean($_GET['id']);
	$name=clean($_GET['name']);
	$clientname=clean($_GET['clientname']);
	} else {
	echo "<p>Sensor ID is missing...</p>";
	exit();
}
$showFromDate = time() - 86400;// * $config['chart_max_days'];; // 864000 => 24 hours * 10 days

echo "<div style='margin-bottom:25px;'><div style='text-align:center;'>";
echo "<h5>$clientname</h5></div>";

echo "<div class='btn-group'>";
	echo "<a href='index.php' class='btn btn-default active' role='button'><span class='glyphicon glyphicon-arrow-left'></span> {$lang['Return']}</a>";
echo "</div>";

echo "<div id='container' style='height: 650px; margin: 0 auto'></div>"; // tells where to put the chart

unset($temp_values);
$joinValues = "";
unset($hum_values);      // added humidity variables
$humValues = "";      // added humidity variables
unset($showHumidity);
unset ($sensorDataNow);

/* Get sensordata and generate graph
--------------------------------------------------------------------------- */
$queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' AND time_updated > '$showFromDate' ORDER BY time_updated ASC ";
$resultS = $mysqli->query($queryS);
$k=1;

while ($sensorData = $resultS->fetch_array()) {
	$db_tempValue = trim($sensorData["temp_value"]);
	$db_humValue = trim($sensorData["humidity_value"]);      //retrive humidity values
	
	$timeJS = $sensorData["time_updated"] * 1000;	//convert time to millisecounds
	$temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";	//create an array with temperature values rounded to 2 desimals
	$hum_values[]         = "[" . $timeJS . "," . round($db_humValue, 2) . "]";      // do something with values
	$sensorDataNow[]=$sensorData["humidity_value"];
	if ($k==1) {
      $time_start=$sensorData["time_updated"];
      $k++;
    };
	$time_stop=$sensorData["time_updated"];
}

$joinValues = join($temp_values, ',');
$joinhumValues = join($hum_values, ',');      // do something more with values
if ($sensorDataNow["[humidity_value]">0]) $showHumidity=1;

echo "<h5><b>" . $lang['Total'] . " " . $lang['since'] . " " . date("Y-m-d H:i", $time_start) . " " . strtolower($lang['To']) . " " . date("Y-m-d H:i", $time_stop) . "</b></h5>";
/* Max, min avrage
--------------------------------------------------------------------------- */
$queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' AND time_updated > '$showFromDate'";
$resultS = $mysqli->query($queryS);
$sensorData = $resultS->fetch_array();

/* Last measurement
--------------------------------------------------------------------------- */
$queryS = "SELECT time_updated, temp_value, humidity_value FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' ORDER BY time_updated DESC LIMIT 1";
$resultS = $mysqli->query($queryS);
$sensorDataNow = $resultS->fetch_array();

echo "<table class='table table-striped table-hover'>";
echo "<tbody>";

// Temperature
echo "<tr>";
echo "<td>".$lang['Temperature']." ".strtolower($lang['Now'])."</td>";
echo "<td>".round($sensorDataNow['temp_value'], 2)." &deg;";
echo "<abbr style='margin-left:20px;' class=\"timeago\" title='".date("Y-m-d H:i", $sensorDataNow['time_updated'])."</abbr>";
echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<td>".$lang['Temperature']." ".strtolower($lang['Now'])."</td>";
echo "<td>".round($sensorDataNow['temp_value'], 2)." &deg;";
echo "<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("Y-m-d H:i", $sensorDataNow['time_updated'])."</abbr>";
echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<td>".$lang['Avrage']." ".strtolower($lang['Temperature'])."</td>";
echo "<td>".round($sensorData['AVG(temp_value)'], 2)." &deg;</td>";
echo "</tr>";

echo "<tr>";
echo "<td>".$lang['Max']." ".strtolower($lang['Temperature'])."</td>";
echo "<td>".round($sensorData['MAX(temp_value)'], 2)." &deg; </td>";
echo "</tr>";

echo "<tr>";
echo "<td>".$lang['Min']." ".strtolower($lang['Temperature'])."</td>";
echo "<td>".round($sensorData['MIN(temp_value)'], 2)." &deg; </td>";
echo "</tr>";

// Humidity
if ($sensorDataNow['humidity_value'] > 0) {
	echo "<tr>";
	echo "<td>".$lang['Humidity']." ".strtolower($lang['Now'])."</td>";
	echo "<td>".round($sensorDataNow['humidity_value'], 2)." %";
	echo "<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("Y-m-d H:i", $sensorDataNow['time_updated'])."</abbr>";
	echo "</td>";
	echo "</tr>";
}
if ($sensorData['AVG(humidity_value)'] > 0) {
	echo "<tr>";
	echo "<td>".$lang['Avrage']." ".strtolower($lang['Humidity'])."</td>";
	echo "<td>".round($sensorData['AVG(humidity_value)'], 2)." %</td>";
	echo "</tr>";
}
if ($sensorData['MAX(humidity_value)'] > 0) {
	echo "<tr>";
	echo "<td>".$lang['Max']." ".strtolower($lang['Humidity'])."</td>";
	echo "<td>".round($sensorData['MAX(humidity_value)'], 2)." %</td>";
	echo "</tr>";
}
if ($sensorData['MIN(humidity_value)'] > 0) {
	echo "<tr>";
	echo "<td>".$lang['Min']." ".strtolower($lang['Humidity'])."</td>";
	echo "<td>".round($sensorData['MIN(humidity_value)'], 2)." %</td>";
	echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";

		// Desides if to plot the humidity or not
		if ($showHumidity==1) {
			$series="series:[{name: '(" .$lang['Temperature'].") {$row['name']}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: '°C'}}, {name: '(" .$lang['Humidity'].") {$row['name']}', type: 'spline', data: [$joinhumValues], color: '#31EBB3', visible: false, yAxis: 1, tooltip: {valueDecimals: 1, valueSuffix: '%'}}]";
		}
		else {
			$series="series:[{name: '(" .$lang['Temperature'].") {$row['name']}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: '°C'}}]";
			}
	
echo <<<end
<script type="text/javascript">
		
$(function () {
Highcharts.setOptions({
	global:{
    	useUTC: false
    }
});
    $('#container').highcharts({

		chart: {
            type: 'spline',
            zoomType: 'x', //makes it possible to zoom in the chart
            pinchType: 'x', //possible to pinch-zoom on touchscreens
            backgroundColor: '#FFFFFF', //sets background color
            shadow: true //makes a shadow around the chart
        },

        title: {
            text: '{$name}'
        },

	    plotOptions: {
	    	spline: {
	    		gapSize: 2
	    	}
	    },

		legend: {
			align: "center",
			layout: "horizontal",
			enabled: true,
			verticalAlign: "bottom",
			borderRadius: 5,
			borderWidth: 1,
			shadow: true,
			borderColor: 'silver'
		},

		xAxis: {
			type: 'datetime',
		},
		
        yAxis: [{
			opposite: false,
            title: {
                text: '{$lang['Temperature']} (°C)',
            },
            labels: {
                formatter: function () {
                    return this.value + '\u00B0C';
                },
                format: '{value}°C',
                    style: {
                    color: '#777'
                },
            },
        }, {
            opposite: true, //puts the yAxis for humidity on the right-hand side
            showEmpty: false, //hides the axis if data not shown
            title: { // added humidity yAxis
                text: '{$lang['Humidity']} (%)',
                   style: {
                    color: '#31EBB3'
                }, // set manual color for yAxis humidity 
            },
            labels: {
                formatter: function () {
                    return this.value + '%';
                },
                format: '{value}%',
                  style: {
                    color: '#31EBB3'
                },
            },
        }],

        $series, 
    });
});
</script>

end;
echo "</div>";
?>