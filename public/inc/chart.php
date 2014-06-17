<script src="../lib/packages/Highstock-2.0.1/js/highstock.js"></script>
<script src="../lib/packages/Highstock-2.0.1/js/modules/exporting.js"></script>

<div class="container">
<?php

/* Get/set parameters
--------------------------------------------------------------------------- */
if (isset($_GET['id'])) {
$getID = clean($_GET['id']);
	} else {
	echo "<p>Sensor ID is missing...</p>";
	exit();
}
$showFromDate = time() - 86400 * $config['chart_max_days'];; // 864000 => 24 hours * 10 days

/* TEMP SENSOR 01: Get sensors
--------------------------------------------------------------------------- */
$query = "SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$getID'";
$result = $mysqli->query($query);
$row = $result->fetch_array();
$sensorID = trim($row['sensor_id']);

echo "<div style='margin-bottom:25px;'><div style='text-align:center;'>";
echo "<h4>{$row['name']}</h4>";
echo "<h5 style='margin-left:10px;'>{$row['clientname']}</h5></div>";
echo "<div style='float:left; margin-top:-45px; margin-left:15px;'><a class='btn' href='index.php'><-- ".$lang['Return']."</a></div></div>";
echo "<div id='test' style='height: 650px; margin: 0 auto'></div>";

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

while ($sensorData = $resultS->fetch_array()) {
	$db_tempValue = trim($sensorData["temp_value"]);
	$db_humValue = trim($sensorData["humidity_value"]);      //retrive humidity values
	
	$timeJS = $sensorData["time_updated"] * 1000;	//convert time to millisecounds
	$temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";	//create an array with temperature values rounded to 2 desimals
	$hum_values[]         = "[" . $timeJS . "," . round($db_humValue, 2) . "]";      // do something with values
	$sensorDataNow[]=$sensorData["humidity_value"];
}

$joinValues = join($temp_values, ',');
$joinhumValues = join($hum_values, ',');      // do something more with values
if ($sensorDataNow["[humidity_value]">0]) $showHumidity=1;

/* Max, min avrage
--------------------------------------------------------------------------- */
echo "<h5>".$lang['Total']."</h5>";

$queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND time_updated > '$showFromDate'";
$resultS = $mysqli->query($queryS);
$sensorData = $resultS->fetch_array();

/* Last measurement
--------------------------------------------------------------------------- */
$queryS = "SELECT time_updated, temp_value, humidity_value FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' ORDER BY time_updated DESC";
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
    $('#test').highcharts('StockChart', {

		chart: {
            type: 'spline',
            zoomType: 'x', //makes it possible to zoom in the chart
            pinchType: 'x', //possible to pinch-zoom on touchscreens
            backgroundColor: '#FFFFFF', //sets background color
            shadow: true //makes a shadow around the chart
        },

        title: {
            text: '{$row["name"]}'
        },

        plotOptions: {
            spline: {
                marker: {
                    enabled: false //hides the datapoints marker
                },
            },
        },

        rangeSelector: {
            enabled: true,
            buttons: [{
                type: 'hour',
                count: 1,
                text: '1h'
            }, {
                type: 'hour',
                count: 12,
                text: '12h'
            }, {
                type: 'day',
                count: 1,
                text: '1d'
            }, {
                type: 'week',
                count: 1,
                text: '1w'
            }, {
                type: 'month',
                count: 1,
                text: '1m'
            }, {
                type: 'month',
                count: 6,
                text: '6m'
            }, {
                type: 'year',
                count: 1,
                text: '1yr'
            }, {
                type: 'all',
                text: 'All'
            }],
            selected: 2
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
        }, 
				{
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

?>
