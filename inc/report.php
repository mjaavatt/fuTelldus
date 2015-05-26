<script src="lib/packages/Highcharts-4.0.1/js/highcharts.js"></script>
<script src="lib/packages/Highcharts-4.0.1/js/modules/exporting.js"></script>
<script src="./lib/packages/jonthornton-jquery-timepicker-1.3.9-1-gfb55d0e/jquery.timepicker.min.js" ></script>
<link href="./lib/packages/jonthornton-jquery-timepicker-1.3.9-1-gfb55d0e/jquery.timepicker.css" rel="stylesheet">

<script>
	
	$(document).ready(function() {
		$('#dateFrom').datepicker({
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
		});
		
		$('#dateTo').datepicker({
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
		});

		$('#timeFrom').timepicker({ 'timeFormat': 'H:i' });
		$('#timeTo').timepicker({ 'timeFormat': 'H:i' });

		$('#tooltip').tooltip();
	});
</script>

<?php

	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
		exit();
	}
	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $sensorID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	/* Get values
	--------------------------------------------------------------------------- */
	if (isset($_POST['submit'])) {
		$sensorID = clean($_POST['sensorID']);

		$dateFrom = clean($_POST['dateFrom']);
		$timeFrom = clean($_POST['timeFrom']);

		$dateTo = clean($_POST['dateTo']);
		$timeTo = clean($_POST['timeTo']);

		$jump = clean($_POST['jump']);

		header("Location: ?page=report&sensorID=$sensorID&dateFrom=$dateFrom&timeFrom=$timeFrom&dateTo=$dateTo&timeTo=$timeTo&jump=$jump");
		exit();
	}

	if (isset($_GET['sensorID'])) {
		$sensorID = clean($_GET['sensorID']);

		$dateFrom = clean($_GET['dateFrom']);
		$timeFrom = clean($_GET['timeFrom']);

		$dateTo = clean($_GET['dateTo']);
		$timeTo = clean($_GET['timeTo']);

		$jump = clean($_GET['jump']);
		if ($jump == 0) $jump = 1;
	} 
	else {
		$dateFrom = date("Y-m-d", strtotime(' -1 day'));
		$timeFrom = "00:00";

		$dateTo = date("Y-m-d");
		$timeTo = "23:59";

		$jump = 4;
	}

	// Create unix timestamps
	list($yearFrom, $monthFrom, $dayFrom) = explode("-", $dateFrom);
	list($hourFrom, $minFrom) = explode(":", $timeFrom);

	list($yearTo, $monthTo, $dayTo) = explode("-", $dateTo);
	list($hourTo, $minTo) = explode(":", $timeTo);

	$dateFrom = mktime($hourFrom, $minFrom, 00, $monthFrom, $dayFrom, $yearFrom);
	$dateTo = mktime($hourTo, $minTo, 00, $monthTo, $dayTo, $yearTo);

	/* Check for errors
	--------------------------------------------------------------------------- */
	if (isset($_GET['sensorID'])) {
		$error = false;

		if ($dateFrom > $dateTo) $error = true;
		if (date("d", $dateFrom) < 1 || date("d", $dateFrom) > 31) $error = true;
		if (date("d", $dateTo) < 1 || date("d", $dateTo) > 31) $error = true;
	}

	echo "<h4>".$lang['Report']."</h4>";

	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
	echo "<form action='?page=report' method='POST'>";
echo "<div class='table-responsive'>";
		echo "<table class='table'>";
			echo "<thead>";
			echo "<tr>";
				echo "<th>".$lang['Sensor']."</th>";
				echo "<th>".$lang['Date from']."</th>";
				echo "<th>".$lang['Date to']."</th>";
				echo "<th>".$lang['Jump']."</th>";
				echo "<th></th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			echo "<tr>";
				echo "<td>";
					$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='".$user['user_id']."' AND monitoring='1' ORDER BY name ASC LIMIT 100";
					$result = $mysqli->query($query);
					echo "<select name='sensorID'>";
					while ($row = $result->fetch_array()) {
						if ($sensorID == $row['sensor_id'])
							echo "<option value='{$row['sensor_id']}' selected='selected'>{$row['sensor_id']}: {$row['name']}</option>";
						else
							echo "<option value='{$row['sensor_id']}'>{$row['sensor_id']}: {$row['name']}</option>";
					}
					echo "</select>";
				echo "</td>";
				echo "<td>";
					echo "<input class='form-control' style='width:100px;' type='text' name='dateFrom' id='dateFrom' value='".date("Y-m-d", $dateFrom)."' />";
					echo "<input class='form-control' style='width:60px; margin-left:5px;' type='text' name='timeFrom' id='timeFrom' value='".date("H:i", $dateFrom)."' />";
				echo "</td>";
				echo "<td>";
					echo "<input class='form-control' style='width:100px;' type='text' name='dateTo' id='dateTo' value='".date("Y-m-d", $dateTo)."' />";
					echo "<input class='form-control' style='width:60px; margin-left:5px;' type='text' name='timeTo' id='timeTo' value='".date("H:i", $dateTo)."' />";
				echo "</td>";
				echo "<td>";
					echo "<input class='form-control' style='width:50px' type='text' name='jump' id='jump' value='$jump' /> ";
					echo "<a href='#' id='tooltip' data-toggle='tooltip' data-placement='bottom' title='".$lang['Jump description']."'>?</a>";
				echo "</td>";
				echo "<td><input class='btn btn-primary' type='submit' name='submit' value='".$lang['Show data']."' /></td>";
			echo "</tr>";
			echo "</tbody>";

		echo "</table>";
echo "</div>";
	echo "</form>";
	echo "</fieldset>";


	if (isset($_GET['sensorID']) && !$error) {
		echo "<fieldset>";
		
		echo "<div class='well'>";

		unset($temp_values);
		$joinValues = "";
		unset($hum_values);      // added humidity variables
		$joinhumValues = "";
		unset($sensorDataNow);
		unset($showHumidity);

		/* Get sensorname
		--------------------------------------------------------------------------- */
		$queryS = "SELECT name FROM ".$db_prefix."sensors WHERE sensor_id='$sensorID' ";
		$resultS = $mysqli->query($queryS);
		$nameS = $resultS->fetch_array();
		$name = $nameS['name'];
		
		/* Get sensordata and generate graph
		--------------------------------------------------------------------------- */
		$queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND (time_updated > '$dateFrom' AND time_updated < '$dateTo') ORDER BY time_updated ASC";
		$resultS = $mysqli->query($queryS);

		$count = 1; // Settings count for view data every hour

		while ($sensorData = $resultS->fetch_array()) {
			$db_tempValue = trim($sensorData["temp_value"]);
			$db_humValue = trim($sensorData["humidity_value"]);      //retrive humidity values
		
			$timeJS = $sensorData["time_updated"] * 1000;
			if ($count == 1) {
			$temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";
			$hum_values[]         = "[" . $timeJS . "," . round($db_humValue, 2) . "]";      // do something with values
			$sensorDataNow[]=$sensorData["humidity_value"];
			}

			// Add to count or reset
			if ($count == $jump) $count = 1;
			else $count++;

		}

		$joinValues = join($temp_values, ',');
		$joinhumValues = join($hum_values, ',');      // do something more with values
		if ($sensorDataNow["[humidity_value]">0]) $showHumidity=1;

		/* Desides if to plot the humidity or not
		--------------------------------------------------------------------------- */
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
    $('#container').highcharts({
		chart: {
            type: 'spline',
            zoomType: 'x', //makes it possible to zoom in the chart
            pinchType: 'x', //possible to pinch-zoom on touchscreens
            backgroundColor: '#FFFFFF', //sets background color
            shadow: true //makes a shadow around the chart
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

        series: $series,
		
	    plotOptions: {
	    	spline: {
	    		gapSize: 2
	    	}
	    },
		
		title:{
			text: '$name'
		},
    });
});
</script>

<div id="container" style="height: 600px"></div>    
end;

   echo "</div>";

/* Max, min avrage
--------------------------------------------------------------------------- */
$queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id=$sensorID AND (time_updated > '$dateFrom' AND time_updated < '$dateTo') ";
$resultS = $mysqli->query($queryS);
$sensorData = $resultS->fetch_array();


echo "<h5><b>".$lang['Total']." ".$lang['since']." ".date("Y-m-d H:i",$dateFrom)."</b></h5>";
echo "<table class='table table-striped table-hover'>";
echo "<tbody>";

// Temperature
echo "<tr>";
echo "<td>".$lang['Avrage']." ".strtolower($lang['Temperature'])."</td>";
echo "<td>".round($sensorData['AVG(temp_value)'], 2)." &deg;C</td>";
echo "</tr>";

echo "<tr>";
echo "<td>".$lang['Max']." ".strtolower($lang['Temperature'])."</td>";
echo "<td>".round($sensorData['MAX(temp_value)'], 2)." &deg;C</td>";
echo "</tr>";

echo "<tr>";
echo "<td>".$lang['Min']." ".strtolower($lang['Temperature'])."</td>";
echo "<td>".round($sensorData['MIN(temp_value)'], 2)." &deg;C </td>";
echo "</tr>";

// Humidity
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
echo "</fieldset>";
}		
		/* Show errormessage if error
	--------------------------------------------------------------------------- */
	if ($error) {
		echo "<div class='alert alert-warning'>".$lang['Wrong timeformat']."</div>";	
	}

?>
