<?php
	
	echo "<h3>".$lang['Shared sensors']."</h3>";

	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>{$lang['Sensor added to monitoring']}</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success'>{$lang['Sensor removed from monitoring']}</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success'>{$lang['Data saved']}</div>";
	}

	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
	echo "<legend>{$lang['Add shared sensor']}</legend>";
	echo "<form role='form' action='?page=settings_exec&action=addSensorFromXML' method='POST'>";
		echo "<div class='pull-right'>";
			echo "<button type='submit' class='btn btn-primary'>".$lang['Save data']."</button>";
		echo "</div>";
		echo "<div class='form-group'>";
			echo "<label for='description'>".$lang['Description']."</label>";
			echo "<input type='text' class='form-control' id='description' placeholder='".$lang['Enter']." ".strtolower($lang['Description'])."'>";
		echo "</div>";
		echo "<div class='form-group'>";
			echo "<label for='xml_url'>".$lang['XML URL']."</label>";
			echo "<input type='text' class='form-control' id='xml_url' placeholder='".$lang['Enter']." XML URL'>";
		echo "</div>";
	echo "</form>";
	echo "</fieldset>";

	/* Shared sensors
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<div class='form-group'>";
		echo "<legend>{$lang['Sensors']}</legend>";

		$query = "SELECT * FROM ".$db_prefix."sensors_shared WHERE user_id='{$user['user_id']}' ORDER BY description ASC";
	    $result = $mysqli->query($query);
	    $numRows = $result->num_rows;

	    if ($numRows > 0) {
	    	while($row = $result->fetch_array()) {
				$xmlData = simplexml_load_file($row['url']);
		    	echo "<div style='border-bottom:1px solid #eaeaea'>";
					// Tools
					echo "<div class='pull-right'>";
						echo "<div class='btn-group'>";
							if ($row['show_in_main'] == 1) $toggleClass = "btn-success";
								else $toggleClass = "btn-warning";
							if ($row['disable'] == 1) $toggleClass = "btn-danger";
							echo "<button class='btn $toggleClass dropdown-toggle pull-right' data-toggle='dropdown' type='button'>";
							echo "{$lang['Action']} ";
							echo "<span class='caret'></span>";
							echo "</button>";

						echo "<ul class='dropdown-menu pull-right' role='menu'>";
						if ($row['show_in_main'] == 1)
							echo "<li><a href='?page=settings_exec&action=putOnMainSensorFromXML&id={$row['share_id']}'>Remove from main</a></li>";
						else
						echo "<li><a href='?page=settings_exec&action=putOnMainSensorFromXML&id={$row['share_id']}'>Put on main</a></li>";
						if ($row['disable'] == 1)
							echo "<li><a href='?page=settings_exec&action=disableSensorFromXML&id={$row['share_id']}'>Enable</a></li>";
  	  					else
						echo "<li><a href='?page=settings_exec&action=disableSensorFromXML&id={$row['share_id']}'>Disable</a></li>";
						echo "<li><a href='?page=settings_exec&action=deleteSensorFromXML&id={$row['share_id']}'>Delete</a></li>";
					echo "</ul>";
				echo "</div>";
			echo "</div>";

		echo "<div style='font-size:20px;'>".$row['description']."</div>";
			echo "<div style='font-size:11px;'>";
				echo "<b>{$lang['Sensorname']}:</b> ".$xmlData->sensor->name . "<br />";
				echo "<b>{$lang['Location']}:</b> ".$xmlData->sensor->location . "<br />";
				echo "<b>{$lang['XML URL']}:</b> <a href='{$row['url']}' target='_blank'>".$row['url']."</a>";
		echo "</div>";
   		echo "<div style='display:inline-block; width:100px; margin:10px; font-size:20px;'>";
   			echo "<img style='margin-right:10px;' src='images/thermometer02.png' alt='icon' />";
   			echo $xmlData->sensor->temp . "&deg;";
   		echo "</div>";
   		if ($xmlData->sensor->humidity > 0) {
			echo "<div style='display:inline-block; width:100px; margin:10px; font-size:20px;'>";
				echo "<img style='margin-right:10px;' src='images/water.png' alt='icon' />";
				echo $xmlData->sensor->humidity . "%";
			echo "</div>";
		}

		echo "<div style='font-size:10px'>";
   			echo ago($xmlData->sensor->lastUpdate);
		echo "</div>";
   	echo "</div>";
    }
	} else echo "<div class='alert'>{$lang['Nothing to display']}</div>";
		echo "</div>";
	echo "</fieldset>";

?>