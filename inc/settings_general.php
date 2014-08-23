<?php

	echo "<h3>".$lang['General settings']."</h3>";

	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-info'>{$lang['Data saved']}</div>";
	}
?>

<form role='form' action='?page=settings_exec&action=saveGeneralSettings' method='POST'>
<fieldset>
<div class='form-group'>
	<div class='form-group'>
		<label for='control-label' for='pageTitle'><?php echo $lang['Page title']; ?></label>
			<input type='text' class="form-control" name='pageTitle' id='pageTitle' placeholder='<?php echo $lang["Page title"]; ?>' value='<?php echo $config["pagetitle"]; ?>'>
	</div>

	<div class='form-group'>
		<label for='control-label' for='mail_from'><?php echo $lang["Email"]; ?></label>
			<input type="text" class="form-control" name='mail_from' id="mail_from" placeholder="<?php echo $lang['form-group mailaddress']; ?>" value='<?php echo $config["mail_from"]; ?>'>
		</div>

	<div class='form-group'>
		<label for="control-label" for="chart_max_days"><?php echo $lang['Chart max days']; ?></label>
			<input style='width:50px;' type="text" class="form-control" name='chart_max_days' id="chart_max_days" placeholder="<?php echo $lang['Chart max days']; ?>" value='<?php echo $config["chart_max_days"]; ?>'>
	</div>

	<?php
	echo "<div class='form-group'>";
		echo "<label for='language'>{$lang['Public']} ".strtolower($lang['Language'])."</label>";
			echo "<div class='controls'>";
			$sourcePath = "lib/languages/";
			$sourcePath = utf8_decode($sourcePath); // Encode for æøå-characters
			$handler = opendir($sourcePath);
			echo "<select class='form-control' style='max-width:100px' name='language'>";
				while ($file = readdir($handler)) {
					$file = utf8_encode($file); // Encode for æøå-characters
					list($filename, $ext) = explode(".", $file);
					if ($ext == "php") {
						if ($config['public_page_language'] == $filename)
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
	<hr />
	
	<div class='form-group'>
		<div class="controls pull-right">
			<button type="submit" class="btn btn-primary"><?php echo $lang['Save data']; ?></button>
		</div>
	</div>

</form>
