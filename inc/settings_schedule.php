<?php

	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	/* Set parameters
	--------------------------------------------------------------------------- */
	if ($action == "edit") {
		$query = "SELECT * FROM ".$db_prefix."schedule WHERE notification_id='$getID' LIMIT 1";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array();

	    $sensorID = $row['sensor_id'];
	    $direction = $row['direction'];
	    $warningValue = $row['warning_value'];
	    $type = $row['type'];
	    $repeat_alert = $row['repeat_alert'];
	    $device = $row['device'];
	    $device_set_state = $row['device_set_state'];
	    $send_to_mail = $row['send_to_mail'];
	    $mail_primary = $row['notification_mail_primary'];
	    $mail_secondary = $row['notification_mail_secondary'];

	} else {
		$warning_value = 30;
		$repeat_alert = 60;
		$send_to_mail = 1;
		$mail_primary = $user['mail'];
		$mail_secondary = "";
	}

	echo "<h3>".$lang['Schedule']."</h3>";
echo "<fieldset>";
	echo "<div class='pull-right'>";
		echo "<div class='btn-group'>";
			echo "<a class='btn btn-success' href='?page=settings&view=schedule&action=add' role='button'>".$lang['Create new']."</a>";
		echo "</div>";
	echo "</div>";
echo "</fieldset>";
echo "</br>";


	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-info' role='alert'>".$lang['Data saved']."</div>";
		elseif ($_GET['msg'] == 02) echo "<div class='alert alert-error' role='alert'>".$lang['Deleted']."</div>";
	}

	/* Form
	--------------------------------------------------------------------------- */
	if ($action == "add" || $action == "edit") {
		if ($action == "edit") {
			echo "<div class='alert alert-warning' role='alert'>";
			echo "<form class='form-inline' role='form' action='?page=settings_exec&action=updateSchedule&id=$getID' method='POST'>";
		} else {
			echo "<div class='alert alert-info' role='alert'>";
			echo "<form class='form-inline' role='form' action='?page=settings_exec&action=addSchedule' method='POST'>";
		} ?>

	<fieldset>
	<div class="form-group"> <!-- Sensor -->
		<label for="sensorID"><?php echo $lang['Sensor'] ?></label>
		<?php
		$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='".$user['user_id']."' AND monitoring='1' ORDER BY name ASC LIMIT 100";
		$result = $mysqli->query($query);
		echo "<select class='form-control' id='sensorID' name='sensorID'>";
			while ($row = $result->fetch_array()) {
				if ($sensorID == $row['sensor_id'])
					echo "<option value='{$row['sensor_id']}' selected='selected'>{$row['sensor_id']}: {$row['name']}</option>";
				else
					echo "<option value='{$row['sensor_id']}'>{$row['sensor_id']}: {$row['name']}</option>";
				};
		echo "</select>";
  		?>
	</div> <!-- /Sensor -->
	</fieldset>

	<fieldset>
	<div class="form-group"> <!-- Higher / Lower -->
		<label for="direction"><?php echo $lang['Type'] ?></label>
		<select class="form-control" id="direction" name="direction">
			<?php
			if ($direction == "less") $directionSelectedLess = "selected='selected'";
			if ($direction == "more") $directionSelectedMore = "selected='selected'";
			echo "<option value='more' $directionSelectedMore>{$lang['Higher than']}</option>";
			echo "<option value='less' $directionSelectedLess>{$lang['Lower than']}</option>";
			?>
		</select>
	</div>
	<div class="form-group">
		<label for="warningValue"></label>
		<input class="form-control" type="text" id="warningValue" name="warningValue" value="<?php echo $warningValue ?>">
	</div>
	<div class="form-group">
		<label for="type"></label>
		<select class="form-control" id="type" name="type">
			<?php
			if ($type == "celsius") $typeSelectedCelsius = "selected='selected'";
			if ($type == "humidity") $typeSelectedHumidity = "selected='selected'";
			?>
			<option value="celsius" $typeSelectedCelsius>&deg; <?php echo $lang['Celsius'] ?></option>
			<option value="Humidity" $typeSelectedHumidity>% <?php echo $lang['Humidity'] ?></option>
		</select>
	</div> <!-- /Higher / Lower -->
	</fieldset>
</br>
  	<fieldset>
	<div class="form-group"> <!-- Device action -->
		<label><h4><?php echo $lang['Device action'] ?></h4></label>
		</br>
		<label for="deviceID"><?php echo $lang['Devices'] ?></label>
		<?php
		$query  = "SELECT * FROM ".$db_prefix."devices WHERE user_id='".$user['user_id']."' ORDER BY name ASC LIMIT 100";
		$result = $mysqli->query($query);
		echo "<select class='form-control' id='deviceID' name='deviceID'>";
			while ($row = $result->fetch_array()) {
			if ($device == $row['device_id'])
				echo "<option value='{$row['device_id']}' selected='selected'>{$row['device_id']}: {$row['name']}</option>";
			else
				echo "<option value='{$row['device_id']}'>{$row['device_id']}: {$row['name']}</option>";
			};
		echo "</select>";
  		?>
		<select class="form-control" id="device_action" name="device_action">
			<option><?php echo $lang['On'] ?></option>
			<option><?php echo $lang['Off'] ?></option>
		</select>
	</div> <!-- /Device action -->
	</fieldset>
  
	<fieldset>
	<div class="form-group"> <!-- Notifications -->
		<label for="repeat"><?php echo $lang['Notifications'] ?></label>
		<div class="input-group">
			<span class="input-group-addon"><?php echo $lang['Repeat every'] ?></span>
			<input type="text" class="form-control" id="repeat" name="repeat"  value="<?php echo $repeat_alert ?>">
			<span class="input-group-addon"><?php echo $lang['minutes'] ?></span>
		</div>
	</div> <!-- /Notifications -->
    </fieldset>
  
	<fieldset>
	<div class="form-group"> <!-- Send to -->
		<label for="sendTo_mail"><?php echo $lang['Send to'] ?></label>
		<div class="checkbox">
			<?php
			if ($send_to_mail == 1) $sendToMailChecked = "checked='checked'";
			echo "<input type='checkbox' id='sendTo_mail' name='sendTo_mail' value='1' $sendToMailChecked>{$lang['Email']}";
			?>
		</div>
	</div> <!-- /Send to -->
	</fieldset>

	<fieldset>
	<div class="form-group"> <!-- Primary mail -->
		<label for="primary_mail"><?php echo "{$lang['Primary']} {$lang['Email']}" ?></label>
		<input type="email" class="form-control" id="primary_mail" name="mail_primary" value="<?php echo $mail_primary ?>">
	</div> <!-- /Primary mail -->
	<div class="form-group"> <!-- Secondary mail -->
		<label for="secondary_mail"><?php echo "{$lang['Secondary']} {$lang['Email']}" ?></label>
		<input type="email" class="form-control" id="secondary_mail" name="mail_secondary" value="<?php echo $mail_secondary ?>">
	</div> <!-- /Primary mail -->
    </fieldset>

</br>
  
  	<fieldset>
	<div class="form-group"> <!-- Submit -->
		<div style="pull-right">
			<?php
			if ($action == "edit") echo "<a class='btn btn-warning' href='?page=settings&view=schedule'>{$lang['Cancel']}</a> &nbsp";
			?>
			<input class="btn btn-primary" type="submit" id="submit" name="submit" value=<?php echo $lang['Save data'] ?>>
		</div>
	</div> <!-- /Submit -->
	</fieldset>
</form>
<?php
	}
echo "</div>";

	/* Show notifications
	--------------------------------------------------------------------------- */
	$query = "SELECT * 
			  FROM ".$db_prefix."schedule 
			  INNER JOIN ".$db_prefix."sensors ON ".$db_prefix."schedule.sensor_id = ".$db_prefix."sensors.sensor_id
			  WHERE ".$db_prefix."schedule.user_id='{$user['user_id']}' 
			  ORDER BY ".$db_prefix."schedule.sensor_id ASC";
    $result = $mysqli->query($query);
    $numRows = $result->num_rows;

    if ($numRows > 0) {
		echo "<div class='table-responsive'>";

    	echo "<table class='table table-striped table-hover'>";
			echo "<thead>";
				echo "<tr>";
					//echo "<th>".$lang['Name']."</th>";
					echo "<th>".$lang['Rule']."</th>";
					echo "<th>".$lang['Email']."</th>";
					echo "<th>".$lang['Repeat every']."</th>";
					echo "<th>".$lang['Last sent']."</th>";
					echo "<th></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
		    	while($row = $result->fetch_array()) {
		    		echo "<tr>";
		    			echo "<td>";
		    				// Sensorname
		    				echo "#{$row['sensor_id']}: {$row['name']}<br />";
		    				// Rule description
		    				if ($row['direction'] == "less") $directionDesc = $lang['Lower than'];
		    				elseif ($row['direction'] == "more") $directionDesc = $lang['Higher than'];
		    				if ($row['type'] == "celsius") {
		    					$typeDesc = $lang['Temperature'];
		    					$unit = "&deg;";
		    				}
		    				elseif ($row['type'] == "humidity") {
		    					$typeDesc = $lang['Humidity'];
		    					$unit = "%";
		    				}
		    				echo "{$lang['If']} <b>$typeDesc</b> ".strtolower($lang['Is'])." <b>$directionDesc</b>  <b>{$row['warning_value']}" . $unit ."C ".strtolower($lang['Than'])."</b>";
		    				if (!empty($row['device'])) {
		    					$getDeviceName = getField("name", "".$db_prefix."devices", "WHERE device_id='{$row['device']}'");
		    					echo "<br />";
			    				echo "$getDeviceName";
			    				if ($row['device_set_state'] == 1) echo " &nbsp; ({$lang['On']})";
			    				elseif ($row['device_set_state'] == 0) echo " &nbsp; ({$lang['Off']})";
			    			}
		    			echo "</td>";
		    			// Send to mail
		    			echo "<td style='text-align:center;'>";
		    				if ($row['send_to_mail'] == 1) echo "<img style='height:16px;' src='images/metro_black/check.png' alt='yes' />";
		    				else echo "<img style='height:16px;' src='images/metro_black/cancel.png' alt='no' />";
		    			echo "</td>";
		    			// Repeat every
		    			echo "<td>";
		    				echo "{$row['repeat_alert']} {$lang['minutes']}";
		    			echo "</td>";
		    			// Time since last warning
		    			echo "<td>";
		    				if ($row['last_warning'] > 0) echo ago($row['last_warning']);
		    			echo "</td>";
		    			// Toggle tools
		    			echo "<td>";
							echo "<div class='btn-group'>";
								echo "<button type='button class='btn btn-default dropdown-toggle' data-toggle='dropdown'>";
									echo "<span class='caret'></span>";
								echo "</button>";
								echo "<ul class='dropdown-menu pull-right'>";
					    			echo "<li><a href='?page=settings&view=schedule&action=edit&id={$row['notification_id']}'>".$lang['Edit']."</a></li>";
					    			echo "<li><a href='?page=settings_exec&action=deleteSchedule&id={$row['notification_id']}'>".$lang['Delete']."</a></li>";
								echo "</ul>";
							echo "</div>";
		    			echo "</td>";
		    		echo "</tr>";
		    	}
    		echo "</tbody>";
    	echo "</table>";
		echo "</div>";
    }
    else echo "<div class='alert'>{$lang['Nothing to display']}</div>";

?>