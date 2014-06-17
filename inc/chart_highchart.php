<script src="lib/packages/Highcharts-4.0.1/js/highcharts.js"></script>
<script src="lib/packages/Highcharts-4.0.1/js/modules/exporting.js"></script>

<?php

    // Set how long back you want to pull data
    $showFromDate = time() - 86400 * $config['chart_max_days']; // 864000 => 24 hours * 10 days

    /* TEMP SENSOR 01: Get sensors
    --------------------------------------------------------------------------- */
    $query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_array()) {

		echo "<div class='well'>";

        unset($temp_values);
        $joinValues = "";
        unset($hum_values);      // added humidity variables
        $joinhumValues = "";      // added humidity variables
		unset ($sensorDataNow);
		unset($showHumidity);



        /* Get sensordata and generate graph
        --------------------------------------------------------------------------- */
        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row["sensor_id"]}' AND time_updated > '$showFromDate' ORDER BY time_updated ASC";
        $resultS = $mysqli->query($queryS);
      
        while ($sensorData = $resultS->fetch_array()) {
            $db_tempValue = trim($sensorData["temp_value"]);
         	$db_humValue = trim($sensorData["humidity_value"]);      //retrive humidity values

            $timeJS = $sensorData["time_updated"] * 1000;
            $temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";
         	$hum_values[]         = "[" . $timeJS . "," . round($db_humValue, 2) . "]";      // do something with values
			$sensorDataNow[]=$sensorData["humidity_value"];
        }

		if ($sensorDataNow["[humidity_value]">0]) $showHumidity=1;
        $joinValues = join($temp_values, ',');
        $joinhumValues = join($hum_values, ',');      // do something more with values
		
		// Desides if to plot the humidity or not
		if ($showHumidity==1) {
			$series="[{name: '(" .$lang['Temperature'].") {$row['name']}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: '°C'}}, {name: '(" .$lang['Humidity'].") {$row['name']}', type: 'spline', dashStyle: 'shortdot', data: [$joinhumValues], color: '#31EBB3', visible: false, yAxis: 1, tooltip: {valueDecimals: 1, valueSuffix: '%'}}]";
		}
		else {
			$series="[{name: '(" .$lang['Temperature'].") {$row['name']}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: '°C'}}]";
			};



echo <<<end
<script type="text/javascript">

$(function () {
Highcharts.setOptions({
	global:{
    	useUTC: false
    }
});
    $('#{$row["sensor_id"]}').highcharts({
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

        legend: {
            align: "center",
            layout: "horizontal",
            enabled: true,
            verticalAlign: "bottom",
        },

        xAxis: {
            type: 'datetime',
        },

        yAxis: [{
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

        series: $series,
    });
})
;</script>

<div id="{$row["sensor_id"]}" style="height: 600px"></div>    
end;

/* Max, min avrage
--------------------------------------------------------------------------- */
echo "<h5>".$lang['Total']." $row[name]</h5>";
$queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row["sensor_id"]}' AND time_updated > '$showFromDate'";
$resultS = $mysqli->query($queryS);
$sensorData = $resultS->fetch_array();

/* Last measurement
--------------------------------------------------------------------------- */
$queryS = "SELECT time_updated, temp_value, humidity_value FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row["sensor_id"]}' ORDER BY time_updated DESC";
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

    }

?>
