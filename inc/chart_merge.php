<script src="lib/packages/Highstock-2.0.1/js/highstock.js"></script>
<script src="lib/packages/Highstock-2.0.1/js/modules/exporting.js"></script>

<?php

	// Set how long back you want to pull data
	$showFromDate = time() - 86400*$config['chart_max_days']; // 86400 => 24 hours 

	/* TEMP SENSOR 01: Get sensors
	--------------------------------------------------------------------------- */
	$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
	$result = $mysqli->query($query);

	$count=0; // added counter to count the senesors
	while ($row = $result->fetch_array()) {
		echo "<div class='container'>";
		unset($temp_values);
		$joinValues = "";
		unset($hum_values);      // added humidity variables
		$joinhumValues = "";
		unset($showHumidity);
		unset ($sensorDataNow);
		
		/* Get sensordata and generate graph
		--------------------------------------------------------------------------- */
		$queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row["sensor_id"]}' AND time_updated > '$showFromDate' ORDER BY time_updated ASC ";
		$resultS = $mysqli->query($queryS);

		while ($sensorData = $resultS->fetch_array()) {
			$db_tempValue = trim($sensorData["temp_value"]);
			$db_humValue = trim($sensorData["humidity_value"]);      //retrive humidity values
		
			$timeJS = $sensorData["time_updated"] * 1000;
			$temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";
			$hum_values[]         = "[" . $timeJS . "," . round($db_humValue, 2) . "]";      // do something with values
			$sensorDataNow[]=$sensorData["humidity_value"];
		}
		if ($sensorDataNow["[humidity_value]">0]) $showHumidity=1;	// Looks fore humidity greater then 0
		$joinValues = join($temp_values, ',');
		$joinhumValues = join($hum_values, ',');      // do something more with values

		// Desides if to plot the humidity or not
		if ($showHumidity==1) {
			$seriesOptions [$count] = "{name: '(" .$lang['Temperature'].") {$row['name']}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: 'Â°C'}}";
			$count++;
			$seriesOptions[$count]="{name: '(" .$lang['Humidity'].") {$row['name']}', type: 'spline', dashStyle: 'shortdot', data: [$joinhumValues], visible: false, yAxis: 1, tooltip: {valueDecimals: 1, valueSuffix: '%'}}";
			$count++;
		}
		else {
			$seriesOptions [$count]= "{name: '(" .$lang['Temperature'].") {$row['name']}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: 'Â°C'}}";
			$count++;
			}
		echo "</div>";
	}
	rsort($seriesOptions); // sorts the sensors
	$joinSeriesData= join($seriesOptions, ',');
	$seriesData="[$joinSeriesData]";

echo <<<end
<script type="text/javascript">
		
$(function () {
Highcharts.setOptions({
	global:{
    	useUTC: false
        }
});
	$('#container').highcharts('StockChart', {

		chart: {
            type: 'spline',
            zoomType: 'x', //makes it possible to zoom in the chart
            pinchType: 'x', //possible to pinch-zoom on touchscreens
            backgroundColor: '#FFFFFF', //sets background color
            shadow: true //makes a shadow around the chart
        },
        
        rangeSelector: {
        	enabled: true,
        	buttons:[{
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

        title: {
            text: '{$lang['Combine charts']}'
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
			shadow: true,
			borderColor: 'silver',
			borderWidth: 1,
			borderRadius: 5
		},

        xAxis: {
            type: 'datetime',
        },
		
        yAxis: [{
			opposite: false,
            title: {
                text: '{$lang['Temperature']} (Â°C)',
            },
            labels: {
                formatter: function () {
                    return this.value + '\u00B0C';
                },
                format: '{value}Â°C',
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

        series: $seriesData,
	    });
});
</script>

<div id="container" style="height: 600px"></div>    
end;

?>
