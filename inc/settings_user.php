<?php
	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	/* Check access
	--------------------------------------------------------------------------- */
	if ($user['admin'] != 1) {
		if ($getID != $user['user_id']) {
			header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
			exit();
		}
	}

	// Check for action or user is
	if (!isset($_GET['id'])) {
		if (!isset($_GET['action'])) {
			header("Location: ?page=settings&view=users");
			exit();
		}
	}

	/* Get userdata
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."users WHERE user_id='".$getID."'");
	$selectedUser = $result->fetch_array();

	/* Get user telldus-config
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."users_telldus_config WHERE user_id='".$getID."'");
	$selectedUserTelldusConf = $result->fetch_array();
	
	echo "<h3>".$lang['Userprofile']."</h3>";

	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-info'>".$lang['Userdata updated']."</div>";
		elseif ($_GET['msg'] == 02) echo "<div class='alert alert-warning'>".$lang['Old password is wrong']."</div>";
		elseif ($_GET['msg'] == 03) echo "<div class='alert alert-warning'>".$lang['New password does not match']."</div>";
	}

	if ($action == "edit") {
		echo "<div class='alert alert-warning' role='alert'>";
		echo "<form role='form' action='?page=settings_exec&action=userSave&id=$getID' method='POST'>";
	} else {
		echo "<div class='alert alert-info' role='alert'>";
		echo "<form role='form' action='?page=settings_exec&action=userAdd' method='POST'>";
	}
?>

	<fieldset>
		<legend><?php echo $lang['Login']; ?></legend>
		<div class="form-group">
			<label for='control-label' for="inputEmail"><?php echo $lang['Email']; ?></label>
			<input type="text" class="form-control" name="inputEmail" id="inputEmail" placeholder="<?php echo $lang['Email']; ?>" value="<?php echo $selectedUser['mail']; ?>">
			<p class="help-block"><?php echo $lang['Leave field to keep current']; ?></p>
		</div>

		<div class="form-group">
			<label for='control-label' for="inputPassword"><?php echo $lang['New'] . " " . strtolower($lang['Password']); ?> </label>
			<input type="password" class="form-control" name='newPassword' id="newPassword" placeholder="<?php echo $lang['New'] . " " . strtolower($lang['Password']); ?>" autocomplete="off">
		</div>

		<div class="form-group">
			<label for='control-label' for="inputPassword"><?php echo $lang['Repeat'] . " " . strtolower($lang['Password']); ?></label>
			<input type="password" class="form-control" name='newCPassword' id="newCPassword" placeholder="<?php echo $lang['Repeat'] . " " . strtolower($lang['Password']); ?>" autocomplete="off">
		</div>

	<div class="checkbox">
		<label>
			<input type="checkbox" name="adminChecked" value="">
			<?php echo $lang['Admin']; ?>
		</label>
	</div>
	</fieldset>

	<?php
		echo "<fieldset>";
			echo "<legend>{$lang['Chart']}</legend>";
			echo "<div class='form-group'>";
				echo "<label for='control-label' for='selectChart'>".$lang['Select chart']."</label>";
				echo "<div class='controls'>";
				echo "<select class='form-control' style='max-width:200px;' name='selectChart'>"; //[]' id='selectChart' multiple
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
					$i = 0;
					while ($i < count($sensors)) {
						if ($selectedUser['chart_type'] == $sensors[$i][0]) {
							$selected = 'selected';
						}
						else {
							$selected = '';
						};
						echo "<option $selected value={$sensors[$i][0]}>{$sensors[$i][1]}</option>";
						$i++;
					};
					echo "<option disabled>──────────</option>";
					if ($selectedUser['chart_type'] == 'mergeCharts') $selectedChart = "selected='selected'";
					else $selectedChart = "";
					echo "<option value='mergeCharts' $selectedChart>{$lang['Combine charts']}</option>";
				echo "</select>";
			echo "</div>";
		echo "</fieldset>";	
	?>

<fieldset>
		<legend><?php echo $lang['Language']; ?></legend>
		<?php
			echo "<div class='form-group'>";
				echo "<label for='language'>".$lang['User language']."</label>";
				echo "<div class='controls'>";
					$sourcePath = "lib/languages/";
					$sourcePath = utf8_decode($sourcePath); // Encode for æøå-characters
					$handler = opendir($sourcePath);
					echo "<select class='form-control' style='max-width:100px' name='language'>";
					while ($file = readdir($handler)) {
						$file = utf8_encode($file); // Encode for æøå-characters
						list($filename, $ext) = explode(".", $file);
						if ($ext == "php") {
						if ($defaultLang == $filename)
							echo "<option value='$filename' selected='selected'>$filename</option>";
						else
							echo "<option value='$filename'>$filename</option>";
						}
					}
				echo "</select>";
			echo "</label>";
		echo "</div>";
		?>
	</fieldset>

	<fieldset>
		<legend>Telldus</legend>
		<?php
			echo "<div class='form-group'>";
				echo "<label for='syncLists'>".$lang['Sync lists everytime']."</label>";
				echo "<div class='controls'>";
					if ($selectedUserTelldusConf['sync_from_telldus'] == 1) $syncTelldusOn = "checked='checked'";
					else $syncTelldusOff = "checked='checked'";
					echo "<label class='radio-inline'>";
					echo "<input type='radio' name='syncTelldusLists' id='syncTelldusListsOn' value='1' $syncTelldusOn> {$lang['On']}";
					echo "</label>";
					echo "<label class='radio-inline'>";
					echo "<input type='radio' name='syncTelldusLists' id='syncTelldusListsOff' value='0' $syncTelldusOff> {$lang['Off']}";
					echo "</label>";
				echo "</div>";
			echo "</div>";
		?>
		<div class="form-group">
			<label for='control-label' for="public_key"><?php echo $lang['Public key']; ?></label>
			<input style='max-width:350px;' type="text" class="form-control" name='public_key' id="public_key" placeholder="<?php echo $lang['Public key']; ?>" value='<?php echo $selectedUserTelldusConf['public_key']; ?>'>
		</div>

		<div class="form-group">
			<label for='control-label' for="private_key"><?php echo $lang['Private key']; ?></label>
			<input style='max-width:350px;' type="text" class="form-control" name='private_key' id="private_key" placeholder="<?php echo $lang['Private key']; ?>" value='<?php echo $selectedUserTelldusConf['private_key']; ?>'>
		</div>

		<div class="form-group">
			<label for='control-label' for="token_key"><?php echo $lang['Token']; ?></label>
			<input style='max-width:350px;' type="text" class="form-control" name='token_key' id="token_key" placeholder="<?php echo $lang['Token']; ?>" value='<?php echo $selectedUserTelldusConf['token']; ?>'>
		</div>

		<div class="form-group">
			<label for='control-label' for="token_secret_key"><?php echo $lang['Token secret']; ?></label>
			<input style='max-width:350px;' type="text" class="form-control" name='token_secret_key' id="token_secret_key" placeholder="<?php echo $lang['Token secret']; ?>" value='<?php echo $selectedUserTelldusConf['token_secret']; ?>'>
		</div>
	</fieldset>

	<div class="form-group">
		<div class="controls pull-right">
			<?php
				if ($getID == $user['user_id']) {
					echo "<a class='btn btn-warning' style='margin-right:15px;' href='login/logout.php' onclick=\"return confirm('Are you sure?')\">".$lang['Log out']."</a>";
				}
				if ($action == "edit") {
					echo "<button type='submit' class='btn btn-primary'>".$lang['Save data']."</button>";
				} else {
					echo "<button type='submit' class='btn btn-success'>".$lang['Create user']."</button>";
				}
			?>	
		</div>
	</div>
</br>
</form>
</div>
