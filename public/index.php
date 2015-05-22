<?php
	require("../lib/base.inc.php");

ini_set('include_path', '/volume1/web/fuTelldus/public');

	/* Check for public sensors
	--------------------------------------------------------------------------- */
	$query = "SELECT * FROM ".$db_prefix."sensors WHERE monitoring='1' AND public='1'";
    $result = $mysqli->query($query);
    $numRows = $result->num_rows;

    if ($numRows == 0) {
    	header("Location: ../login/?msg=03");
    	exit();
    }

    /* Get public language
	--------------------------------------------------------------------------- */
	include("../lib/languages/".$config['public_page_language'].".php");

?>

<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="utf-8">
    <title><?php echo $config['pagetitle']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

	<!-- Bootstrap framework -->
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>	

	<!-- Jquery -->
	<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="http://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
	<link href="http://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
	<script src="../lib/packages/timeago_jquery/jquery.timeago.js"></script>
	<?php
		if ($defaultLang == "sw") echo "<script src=\"../lib/packages/timeago_jquery/jquery.timeago.sw.js\"></script>";
	?>

	<!-- For iPhone 4 Retina display: -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../images/thermometer.png">

	<!-- For iPad: -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../images/thermometer.png">

	<!-- For iPhone: -->
	<link rel="apple-touch-icon-precomposed" href="../images/thermometer.png">

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

</head>
<body>
<div class="container">
	
	<nav class="navbar navbar-default" style="margin-top:30px" role="navigation">
		<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" style='color:#0088cc; font-weight:bold' href="../public/index.php">fuTelldus</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="navbar">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="../login/"><?php echo "<span class='glyphicon glyphicon-off'></span> "; echo $lang['Log in']; ?></a></li>
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
