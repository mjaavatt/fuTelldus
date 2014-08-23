<?php

  require("../lib/base.inc.php");

  
  // Auto login with remember-me cookie
  if (isset($_COOKIE["fuTelldus_user_loggedin"])) {
    $_SESSION['fuTelldus_user_loggedin'] = $_COOKIE["fuTelldus_user_loggedin"];
    header("Location: ../index.php");
    exit();
  }

?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <title><?php echo $config['pagetitle']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">


  

    <!-- Jquery -->
    <script src="../lib/packages/jquery/jquery-2.1.1.min.js"></script>

    <!-- Bootstrap framework -->
    <script src="../lib/packages/bootstrap/js/bootstrap.min.js"></script>
    <link href="../lib/packages/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--  <link href="../lib/packages/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet"> -->


    <link href="css/signin.css" rel="stylesheet">


</head>

<body>

<div class="container">

	<form class="form-signin" role="form" action="login_exec.php" method="POST">
        <h2 class="form-signin-heading"><?php echo $config['pagetitle']; ?></h2>
        <?php
          if (isset($_GET['msg'])) {
              if ($_GET['msg'] == 01) echo "<div class='alert alert-error'>Wrong username and/or password</div>";
              if ($_GET['msg'] == 02) echo "<div class='alert alert-info'>You logged out</div>";
              if ($_GET['msg'] == 03) echo "<div class='alert alert-error'>No public sensors active</div>";
          }
        ?>
		
        <input type="text" class="form-control" name="mail" placeholder="Email address">
        <input type="password" class="form-control" name="password" placeholder="Password" required>

        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>

        <div class="pull-right">

            <?php
              $query = "SELECT * FROM ".$db_prefix."sensors WHERE monitoring='1' AND public='1'";
              $result = $mysqli->query($query);
              $numRows = $result->num_rows;

              if ($numRows > 0) {
                echo "<a style='margin-right:10px;' href='../public/'>{$lang['View public sensors']}</a>";
              }
            ?>

            <button class="btn btn-lg btn-primary" type="submit">Sign in</button>
        </div>

        <div style="clear:both;"></div>

        <?php
          // Create a random key to secure the login from this form!
          $_SESSION['secure_fuCRM_loginForm'] = "fuTelldus3sfFwer35tF36¤234%&".time()."254543";
          $hashSecureFormLogin = hash('sha256', $_SESSION['secure_fuTelldus_loginForm']);
          echo "<input type='hidden' name='uniq' value='$hashSecureFormLogin' />";
        ?>
    </form>

</div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	
</body>
</html>
