<script src="lib/packages/Highcharts-4.0.3/js/highcharts.js"></script>
<script src="lib/packages/Highcharts-4.0.3/js/modules/exporting.js"></script>
<script src="lib/packages/jonthornton-jquery-timepicker/jquery.timepicker.min.js" ></script>
<link href="lib/packages/jonthornton-jquery-timepicker/jquery.timepicker.css" rel="stylesheet">
<script src="lib/packages/jquery/jquery.ui.datepicker-sv.js"></script> <!-- Swedish languagefile for jquery-datepicker -->

<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js" ></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css" rel="stylesheet">

<script src="lib/packages/export-csv/export-csv.js" ></script>

<script>
	$(document).ready(function() {
		$.datepicker.setDefaults($.datepicker.regional['sv']);
		$('#dateFrom').datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 2,	// shows 2 months
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
			showOtherMonths: true,
			selectOtherMonths: true
		});
		
		$('#dateTo').datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 2,	// shows 2 months
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
			showOtherMonths: true,
			selectOtherMonths: true
		});

		$('#timeFrom').timepicker({ 'timeFormat': 'H:i' });
		$('#timeTo').timepicker({ 'timeFormat': 'H:i' });

		$('#tooltip').tooltip();
	});
</script>

<script type="text/javascript">
	$(window).on('load', function () {
		
		$('.selectpicker').selectpicker();
	    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
    		$('.selectpicker').selectpicker('mobile');
    	};
	});
</script>

<?php
/* Get chosen sensor ID
--------------------------------------------------------------------------- */
if (isset($_GET['id'])) {
	$sensorID[0][0] = clean($_GET['id']);
};

/* Get values
--------------------------------------------------------------------------- */
if (isset($_POST['submit'])) {
	$sensorID[] = $_POST['sensorID'];
	clean($_POST['sensorID']);
	$dateFrom = clean($_POST['dateFrom']);
	$timeFrom = clean($_POST['timeFrom']);
	$dateTo = clean($_POST['dateTo']);
	$timeTo = clean($_POST['timeTo']);
	$jump = clean($_POST['jump']);
	$i = 0;
	foreach($sensorID as $v1) {
		foreach($v1 as $v2) {
			$sensorID_inv[$i++][0] = $v2;
		}
	}

	unset($v1);
	unset($v2);
	$sensorID = $sensorID_inv;
};

/* Get a list of all the sensors monitored
--------------------------------------------------------------------------- */
if (!isset($sensorID)) {
	$query = "SELECT * FROM " . $db_prefix . "users WHERE user_id='{$user['user_id']}'";
	$result = $mysqli->query($query);
	$row = $result->fetch_array();
	if (substr($row['chart_type'], 0, 11) == 'mergeCharts') {
		$query = "SELECT * FROM ".$db_prefix."sensors WHERE monitoring='1' ORDER BY name ASC LIMIT 100";
		$result = $mysqli->query($query);
		$i = 0;
		while ($row = $result->fetch_array()) {
			$sensor_id = $row['sensor_id'];
			$sensor_name = $row['name'];
			$sensorID[$i][0] = $sensor_id; // agregate all sensor id´s and names in one array
			$sensorID[$i][1] = $sensor_name;
			$i++;
		};
	} else {
		$sensorID[0][0] = $row['chart_type'];
	};
};

if (isset($_GET['action'])) $action = clean($_GET['action']);

if ($action == 'edit' || count($sensorID)>1) {
	$i = 0;
	while ($i < count($sensorID)) {
		/* Set parameters
		--------------------------------------------------------------------------- */
		$query = "SELECT * FROM " . $db_prefix . "sensors WHERE sensor_id='{$sensorID[$i][0]}' LIMIT 1";
		$result = $mysqli->query($query);
		$row = $result->fetch_array();
		$clientname = $row['clientname'];
		$sensorID[$i][0] = $sensorID[$i][0]; // Sensor ID
		$sensorID[$i][1] = $row['name']; // Sensor name
		$i++;
	};
}
else {
	/* Set parameters
	--------------------------------------------------------------------------- */
	$query = "SELECT * FROM " . $db_prefix . "sensors WHERE sensor_id='{$sensorID[0][0]}' LIMIT 1";
	$result = $mysqli->query($query);
	$row = $result->fetch_array();
	$sensorID[0][1] = $row['name'];
	$clientname = $row['clientname'];
};

/* Sets start date if not chosen
--------------------------------------------------------------------------- */
if (!isset($dateFrom)) {
	$dateFrom = date("Y-m-d", strtotime(' -1 day'));
	$timeFrom = "00:00";
	$dateTo = date("Y-m-d");
	$timeTo = "23:59";
	$jump = 4;
}

/* Create unix timestamps
--------------------------------------------------------------------------- */
list($yearFrom, $monthFrom, $dayFrom) = explode("-", $dateFrom);
list($hourFrom, $minFrom) = explode(":", $timeFrom);
list($yearTo, $monthTo, $dayTo) = explode("-", $dateTo);
list($hourTo, $minTo) = explode(":", $timeTo);
$dateFrom = mktime($hourFrom, $minFrom, 00, $monthFrom, $dayFrom, $yearFrom);
$dateTo = mktime($hourTo, $minTo, 00, $monthTo, $dayTo, $yearTo);

/* Check for errors
--------------------------------------------------------------------------- */
if (isset($sensorID)) {
	$error = false;
	if ($dateFrom > $dateTo) $error = true;
	if (date("d", $dateFrom) < 1 || date("d", $dateFrom) > 31) $error = true;
	if (date("d", $dateTo) < 1 || date("d", $dateTo) > 31) $error = true;
}

echo "<div style='margin-bottom:25px'><div style='text-align:center;'>";
echo "<h5>$clientname</h5>";
echo "</div>";
?>
	<fieldset>
		<div class="pull-left">
			<button type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#filter"><?php
			echo "<span class='glyphicon glyphicon-ok'></span> ";
			echo $lang['Set filter']; ?></button>
		</div>
	</fieldset>
<?php

/* Filter form
--------------------------------------------------------------------------- */
echo "<div class='collapse' id='filter'>";
	echo "<div class='alert alert-info' role='alert'>";
		echo "<form class='form-inline' role='form' action='?page=chart&action=edit' method='POST'>";
			echo "<div class='table-responsive'>";
				echo "<table class='table'>";
				echo "<thead>";
					echo "<tr>";
						echo "<th>" . $lang['Sensor'] . "</th>";
						echo "<th>" . $lang['Date from'] . "</th>";
						echo "<th>" . $lang['Date to'] . "</th>";
						echo "<th>" . $lang['Jump'] . "</th>";
						echo "<th></th>";
					echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
					echo "<tr>";
						echo "<td>";
							$query = "SELECT * FROM " . $db_prefix . "sensors WHERE user_id='" . $user['user_id'] . "' AND monitoring='1' ORDER BY name ASC LIMIT 100";
							$result = $mysqli->query($query);
							$i = 0;

							while ($row = $result->fetch_array()) {
								$sensor_id = $row['sensor_id'];
								$sensor_name = $row['name'];
								$sensors[$i][0] = $sensor_id; // agregate all sensor id´s and names in one array
								$sensors[$i][1] = $sensor_name;
								$i++;
							};
							echo "<select class='selectpicker' multiple='multiple' class='form-control' name='sensorID[]' id='sensorID' title='".$lang['Chose graph']."'>";
							$i = 0;

								while ($i < count($sensors)) {
									$k = 0;
									while ($k < count($sensorID)) {
										if ($sensors[$i][0] == $sensorID[$k][0]) {
											$selected = 'selected';
											break;
										}
										else {
											$selected = '';
										};
										$k++;
									};
									echo "<option $selected value={$sensors[$i][0]}>{$sensors[$i][1]}</option>";
									$i++;
								};
							echo "</select>";
						echo "</td>";
						echo "<td>";
							echo "<input class='form-control' style='width:100px;' type='text' name='dateFrom' id='dateFrom' value='" . date("Y-m-d", $dateFrom) . "' />";
							echo "<input class='form-control' style='width:65px; margin-left:5px;' type='text' name='timeFrom' id='timeFrom' value='" . date("H:i", $dateFrom) . "' />";
						echo "</td>";
						echo "<td>";
							echo "<input class='form-control' style='width:100px;' type='text' name='dateTo' id='dateTo' value='" . date("Y-m-d", $dateTo) . "' />";
							echo "<input class='form-control' style='width:65px; margin-left:5px;' type='text' name='timeTo' id='timeTo' value='" . date("H:i", $dateTo) . "' />";
						echo "</td>";
						echo "<td>";
							echo "<input class='form-control' style='width:50px' type='text' name='jump' id='jump' value='$jump' /> ";
							echo "<a href='#' id='tooltip' data-toggle='tooltip' data-placement='bottom' title='" . $lang['Jump description'] . "'>?</a>";
						echo "</td>";
						echo "<td>";
							echo "<input class='btn btn-primary' type='submit' name='submit' value='" . $lang['Show data'] . "' />";
						echo "</td>";
					echo "</tr>";
				echo "</tbody>";
			echo "</table>";
			echo "</div>";
		echo "</form>";
	echo "</div>";
echo "</div>"; // end of form
unset($value);

/* Get sensordata
--------------------------------------------------------------------------- */
foreach($sensorID as list($id, $name)) {
	echo "<div class='container'>";
	unset($temp_values);
	$joinValues = "";
	unset($hum_values); // added humidity variables
	$joinhumValues = "";
	unset($showHumidity);
	unset($sensorDataNow);

	$queryS = "SELECT * FROM " . $db_prefix . "sensors_log WHERE sensor_id='$id' AND (time_updated > '$dateFrom' AND time_updated < '$dateTo') ORDER BY time_updated ASC ";
	$resultS = $mysqli->query($queryS);
	$count = 1; // Settings count for view data at different steps
	$k=1;
	while ($sensorData = $resultS->fetch_array()) {
		$db_tempValue = trim($sensorData["temp_value"]);
		$db_humValue = trim($sensorData["humidity_value"]); //retrive humidity values
		$timeJS = $sensorData["time_updated"] * 1000;	// convert time to javascript time
		if ($count == 1) {
			$temp_values[] = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";
			$hum_values[] = "[" . $timeJS . "," . round($db_humValue, 2) . "]"; // do something with values
			$sensorDataNow[] = $sensorData["humidity_value"];
		};
		if ($k==1) {
			$time_start=$sensorData["time_updated"];	// Sets the first read timestamp
			$k++;
		};
		if ($count == $jump) $count = 1;	// Add to count or reset
		else $count++;
		$time_stop=$sensorData["time_updated"];	// Updates the last read timestamp
	};
	if ($sensorDataNow["[humidity_value]" > 0]) $showHumidity = 1; // Looks fore humidity greater then 0
	$joinValues = join($temp_values, ',');
	$joinhumValues = join($hum_values, ','); // do something more with values

	/* Desides if to plot the humidity or not
	--------------------------------------------------------------------------- */
	if ($showHumidity == 1) {
		$seriesOptions[$countSensors] = "{name: '(" . $lang['Temperature'] . ") {$name}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: '°C'}}";
		$countSensors++;
		$seriesOptions[$countSensors] = "{name: '(" . $lang['Humidity'] . ") {$name}', type: 'spline', dashStyle: 'shortdot', data: [$joinhumValues], visible: false, yAxis: 1, tooltip: {valueDecimals: 1, valueSuffix: '%'}}";
		$countSensors++;
	}
	else {
		$seriesOptions[$countSensors] = "{name: '(" . $lang['Temperature'] . ") {$name}', type: 'spline', data: [$joinValues], tooltip: {valueDecimals: 1, valueSuffix: '°C'}}";
		$countSensors++;
	}

	echo "</div>";
};
unset($id);
unset($name);
rsort($seriesOptions); // sorts the sensors in reversed order
$joinSeriesData = join($seriesOptions, ',');
$seriesData = "[$joinSeriesData]";

/* Sets chartname depending if one sensor or many sensors chosen
--------------------------------------------------------------------------- */
if (count($sensorID) > 1) {
	$title = $lang['Combine charts'];
}
else {
	$title = $sensorID[0][1];
}

/* Plots the chart(s)
--------------------------------------------------------------------------- */
echo <<<end
<div>
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

			series: $seriesData,

			plotOptions: {
				spline: {
					gapSize: 2
				}
			},

			title: {
				text: '{$title}'  
			},

		},
		
		function (chart) {
				$('#getcsv').click(function () {
					alert(chart.getCSV());
				});
		});
	});
	</script>

	<div id="container" style="height: 600px">
</div> 
end;
	echo "<button id='getcsv'>{$lang['Export as']} CSV</button>";

/* If one sensor chosen makes a table with max, min, and average values
--------------------------------------------------------------------------- */
if (count($sensorID) == 1) {
	/* Last measurement
	--------------------------------------------------------------------------- */
	$queryS = "SELECT time_updated, temp_value, humidity_value FROM ".$db_prefix."sensors_log WHERE sensor_id={$sensorID[0][0]} ORDER BY time_updated DESC LIMIT 1";
	$resultS = $mysqli->query($queryS);
	$sensorDataNow = $resultS->fetch_array();

	/* Max, min and average
	--------------------------------------------------------------------------- */
	$queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM " . $db_prefix . "sensors_log WHERE sensor_id={$sensorID[0][0]} AND (time_updated > '$dateFrom' AND time_updated < '$dateTo') ";
	$resultS = $mysqli->query($queryS);
	$sensorData = $resultS->fetch_array();
	
	echo "<fieldset>";
	echo "<h5><b>" . $lang['Total'] . " " . $lang['since'] . " " . date("Y-m-d H:i", $time_start) . " " . strtolower($lang['To']) . " " . date("Y-m-d H:i", $time_stop) . "</b></h5>";
	echo "<table class='table table-striped table-hover'>";
		echo "<tbody>";

		// Temperature
		echo "<tr>";
			echo "<td>".$lang['Temperature']." ".strtolower($lang['Now'])."</td>";
			echo "<td>".round($sensorDataNow['temp_value'], 2)." &deg;<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("Y-m-d H:i", $sensorDataNow['time_updated'])."</abbr></td>";
		echo "</tr>";

		echo "<tr>";
			echo "<td>" . $lang['Max'] . " " . strtolower($lang['Temperature']) . "</td>";
			echo "<td>" . round($sensorData['MAX(temp_value)'], 2) . " &deg;C</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>" . $lang['Avrage'] . " " . strtolower($lang['Temperature']) . "</td>";
			echo "<td>" . round($sensorData['AVG(temp_value)'], 2) . " &deg;C</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>" . $lang['Min'] . " " . strtolower($lang['Temperature']) . "</td>";
			echo "<td>" . round($sensorData['MIN(temp_value)'], 2) . " &deg;C </td>";
		echo "</tr>";

		// Humidity
		if ($sensorDataNow['humidity_value'] > 0) {
			echo "<tr>";
				echo "<td>".$lang['Humidity']." ".strtolower($lang['Now'])."</td>";
				echo "<td>".round($sensorDataNow['humidity_value'], 2)." %<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("Y-m-d H:i", $sensorDataNow['time_updated'])."</abbr></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>" . $lang['Max'] . " " . strtolower($lang['Humidity']) . "</td>";
				echo "<td>" . round($sensorData['MAX(humidity_value)'], 2) . " %</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>" . $lang['Avrage'] . " " . strtolower($lang['Humidity']) . "</td>";
				echo "<td>" . round($sensorData['AVG(humidity_value)'], 2) . " %</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>" . $lang['Min'] . " " . strtolower($lang['Humidity']) . "</td>";
				echo "<td>" . round($sensorData['MIN(humidity_value)'], 2) . " %</td>";
			echo "</tr>";
		}

		echo "</tbody>";
	echo "</table>";
	echo "</fieldset>";
};

/* Show errormessage if error
--------------------------------------------------------------------------- */
if ($error) {
	echo "<div class='alert alert-warning'>" . $lang['Wrong timeformat'] . "</div>";
};
?>