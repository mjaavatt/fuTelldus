<?php
	require("lib/base.inc.php");
	require("lib/auth.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="utf-8">
    <title><?php echo $config['pagetitle']; ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap framework -->
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>	
	
	<!-- Jquery -->
	<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<link href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet">
	<script src="lib/packages/timeago_jquery/jquery.timeago.js"></script>
	<?php
		if ($defaultLang == "sw") echo "<script src=\"lib/packages/timeago_jquery/jquery.timeago.sw.js\"></script>";
	?>

	<!-- For iPhone Retina display: -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/thermometer.png">
	<!-- For iPad: -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/thermometer.png">
	<!-- For iPhone: -->
	<link rel="apple-touch-icon-precomposed" href="images/thermometer.png">
	
	<script type="text/javascript">
		idleTime = 0;
		
		$(document).ready(function () {
		    //Increment the idle time counter every minute.
		    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute

		    //Zero the idle timer on mouse movement.
		    $(this).mousemove(function (e) {
		        idleTime = 0;
		    });
		    $(this).keypress(function (e) {
		        idleTime = 0;
		    });
		});
		
		function timerIncrement() {
		    idleTime = idleTime + 1;
		    if (idleTime > 19) { // 20 minutes
		        window.location.reload();
		    }
		}
	</script>
	
	<!-- Fa-strap -->
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
<div class="container">

<nav class="navbar navbar-tabs navbar-default" style="margin-top:30px" role="navigation">
	<div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" style='color:#0088cc; font-weight:bold' href="index.php">fuTelldus</a>
		</div>

    <!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="navbar">
		<ul class="nav navbar-nav">
		<?php
		// Set menuelements as active
		if (!isset($_GET['page']) || $_GET['page'] == "mainpage") $navMainpage_active = "active";
		elseif (substr($_GET['page'], 0, 7) == "sensors") $navSensors_active = "active";
		elseif (substr($_GET['page'], 0, 7) == "devices") $navDevices_active = "active";
		elseif (substr($_GET['page'], 0, 5) == "chart") $navChart_active = "active";
		elseif (substr($_GET['page'], 0, 6) == "report") $navReport_active = "active";
		elseif (substr($_GET['page'], 0, 8) == "settings") $navSettings_active = "active";
		?>
		<li class="<?php echo $navMainpage_active; ?>"><a href="index.php"><?php echo "<span class='glyphicon glyphicon-home'></span> "; echo $lang['Home']; ?></a></li>
		<li class="<?php echo $navSensors_active; ?>"><a href="?page=sensors"><?php echo "<span class='glyphicon glyphicon-dashboard'></span> "; echo $lang['Sensors']; ?></a></li>
		<li class="<?php echo $navDevices_active; ?>"><a href="?page=devices"><?php echo "<i class='fa fa-lightbulb-o fa-lg'></i> "; echo $lang['Lights']; ?></a></li>
		<li class="<?php echo $navChart_active; ?>"><a href="?page=chart"><?php echo "<span class='glyphicon glyphicon-stats'></span> "; echo $lang['Chart']; ?></a>
		<li class="<?php echo $navSettings_active; ?>"><a href="?page=settings"><?php echo "<span class='glyphicon glyphicon-cog'></span> "; echo $lang['Settings']; ?></a></li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
       		<li class="dropdown pull-right">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo "<span class='glyphicon glyphicon-off'></span> "; echo $lang['Signed in as']; echo ": "; echo $user['mail']; ?><b class="caret"></b></a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="?page=settings"><?php echo $lang['My profile']; ?></a></li>
					<li><a href="./public/index.php"><?php echo $lang['View public page']; ?></a></li>
					<li class="divider"></li>
					<li><a href="login/logout.php"><?php echo $lang['Log out']; ?></a></li>
				</ul>
			</li>
		</ul>
	</div><!-- /.navbar-collapse -->
</div><!-- /.container-fluid -->
</nav>

		<?php include("include_script.inc.php"); ?>

		<div class='clearfix'></div>

		<div class='hidden-xs' style='text-align:center; border-top:1px solid #eaeaea; font-size:10px; margin-top:35px; color:#c7c7c7;'>
			Developed by <a href='http://www.fosen-utvikling.no'>Fosen Utvikling</a> &nbsp;&nbsp;
			Last load: <?php echo date("Y-m-d H:i"); ?>
			<br />
			This work is licensed under a <a href='http://creativecommons.org/licenses/by-nc/3.0/'>Creative Commons Attribution-NonCommercial 3.0 Unported License</a>.
		</div>

	</div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	
</body>
</html>
