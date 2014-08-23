<?php

  	/* Get parameters
  	--------------------------------------------------------------------------- */
  	if (isset($_GET['id'])) $getID = clean($_GET['id']);
  	if (isset($_GET['action'])) $action = clean($_GET['action']);
  	
    if (isset($_GET['view'])) {
      $view = clean($_GET['view']);
    } else {
      header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
      exit();
    }

  ?>

  <div class="container-fluid"> <!---fluid"> -->
  	<div class="row">
		<div class="col-md-3">
		 <div class="well">
         <ul class="nav nav-sidebar">
         <li class="nav-header"><?php echo $lang['Settings']; ?></li> 
         <!-- <div class="list-group"> -->
            <?php
              if (!isset($_GET['view']) || $_GET['view'] == "general") $vActive_general = "active";
              elseif (substr($_GET['view'], 0, 4) == "user") $vActive_user = "active";
              elseif (substr($_GET['view'], 0, 5) == "share") $vActive_share = "active";
              elseif (substr($_GET['view'], 0, 13) == "notifications") $vActive_notifications = "active";
              elseif (substr($_GET['view'], 0, 8) == "schedule") $vActive_schedule = "active";
              elseif (substr($_GET['view'], 0, 4) == "cron") $vActive_cron = "active";
              elseif (substr($_GET['view'], 0, 12) == "telldus_test") $vActive_telldusTest = "active";
              elseif (substr($_GET['view'], 0, 5) == "users") $vActive_users = 'active';

              echo "<li class='$vActive_user'><a href='?page=settings&view=user&action=edit&id={$user['user_id']}'>{$lang['Userprofile']}</a></li>";
              echo "<li class='$vActive_share'><a href='?page=settings&view=share'>{$lang['Shared sensors']}</a></li>";
              echo "<li class='$vActive_schedule'><a href='?page=settings&view=schedule'>{$lang['Schedule']}</a></li>";
              //echo "<li class='$vActive_telldusTest'><a href='?page=settings&view=telldus_test'>{$lang['Telldus connection test']}</a></li>";

              if ($user['admin'] == 1) {
                echo "<li class='nav-header'>Admin</li>";
                echo "<li class='$vActive_general'><a href='?page=settings&view=general'>".$lang['General settings']."</a></li>";
                echo "<li class='$vActive_users'><a href='?page=settings&view=users'>".$lang['Users']."</a></li>";
                echo "<li class='$vActive_cron'><a href='?page=settings&view=cron'>".$lang['Test cron-files']."</a></li>";
              }
            ?>
         </ul>
        </div><!--/.well -->
      </div><!--/col-->
		<div class="col-md-9">
			<div class="row placeholders">
      		<?php
      			if (isset($_GET['view'])) {
      				include("inc/settings_" . $view . ".php");
      			} else {
      				include("inc/settings_user.php");
      			}
      		?>
			</div>
		</div>
	</div>
</div>