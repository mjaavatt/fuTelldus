<script>

	jQuery(document).ready(function() {
	  jQuery("abbr.timeago").timeago();
	});

</script>

<style>
	.sensors-wrap {
		padding-left:20px;
	}

	.sensors-wrap .sensor-blocks {
		display:inline-block;
		valign:top;
		margin-right:40px;
		margin-bottom:20px;
		min-width:200px;
		border:0px solid red;
	}

	.sensors-wrap .sensor-blocks img {
		margin-right:10px;
	}

	.sensors-wrap .sensor-name {
		font-size:14px; margin-bottom:0px; font-weight:bold;
	}

	.sensors-wrap .sensor-location {
		font-size:12px; margin-bottom:0px; font-weight:normal;
	}

	.sensors-wrap .sensor-location img {
		height:10px !important;
		margin-left:5px !important;
		margin-right:5px !important;
	}

	.sensors-wrap .sensor-temperature {
		font-size:40px; display:inline-block; valign:top; margin-left:15px; margin-top:6px; margin-bottom:6px; padding-top:10px; border:0px solid red;
	}

	.sensors-wrap .sensor-humidity {
		font-size:40px; display:inline-block; valign:top; margin-left:15px; padding-top:10px; border:0px solid red;
	}

	.sensors-wrap .sensor-timeago {
		font-size:10px; color:#777; text-align: center; padding-top: 8px;
	}

</style>

<?php

	// Margin for desktop and pad
	echo "<div style='height:30px;' class='hidden-xs'></div>";

	// Sensors
	echo "<div class='sensors-wrap'>";
	
		/* My sensors
   		--------------------------------------------------------------------------- */
		$query = "SELECT * FROM ".$db_prefix."sensors WHERE monitoring='1' AND public='1'";
	    $result = $mysqli->query($query);

	    while ($row = $result->fetch_array()) {
	    	
	    	$sensorID = trim($row['sensor_id']);

	    	$queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND time_updated > '$showFromDate' ORDER BY time_updated DESC LIMIT 1";
            $resultS = $mysqli->query($queryS);
            $sensorData = $resultS->fetch_array();


            echo "<div class='sensor-blocks well'>";

            	echo "<div class='sensor-name'>";
            		echo "{$row['name']}";
            	echo "</div>";

            	echo "<div class='sensor-location'>";
            		echo "<img src='../images/location.png' alt='icon' />";
            		echo "{$row['clientname']}";
            	echo "</div>";

            	echo "<div class='sensor-temperature'>";
            		echo "<img src='../images/thermometer02.png' alt='icon' />";
            		echo "{$sensorData['temp_value']}&deg;";
            	echo "</div>";

            	if ($sensorData['humidity_value'] > 0) {
            		echo "<div class='sensor-humidity'>";
	            		echo "<img src='../images/water.png' alt='icon' />";
	            		echo "{$sensorData['humidity_value']}%";
	            	echo "</div>";
            	}

            	echo "<div class='sensor-timeago'>";
            		echo "<abbr class=\"timeago\" title='".date("c", $sensorData['time_updated'])."'>".date("Y-m-d H:i", $sensorData['time_updated'])."</abbr>";
            	echo "</div>";

            	echo "<div style='text-align:left; margin-top:10px;'>";
			echo "<a href='?page=chart&id=$sensorID&name={$row['name']}&clientname={$row['clientname']}'><img style='height:16px;' src='../images/chart_line.png' /> {$lang['View chart']}</a>";
            	echo "</div>";
            echo "</div>";
	    }

	echo "</div>";

?>