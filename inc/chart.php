<?php
    
    if (!$telldusKeysSetup) {
        echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
        exit();
    }

    /* Get chart type
    --------------------------------------------------------------------------- */
    if (empty($user['chart_type'])) $chartType = "highstock";
    else $chartType = $user['chart_type'];

    if (isset($_GET['charttype'])) {
        $chartType = clean($_GET['charttype']);
    }

    if (isset($_GET['view'])) {
        $view = clean($_GET['view']);
    }


    /* Headline
    --------------------------------------------------------------------------- */
    echo "<h3>{$lang['Chart']}</h3>";


    /* Toggle
    --------------------------------------------------------------------------- */
    echo "<div style='float:right; margin-top:-45px; margin-right:20px;' class='btn-group'>";

            echo "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#''>";
			echo "{$lang['Action']}";
            echo "<span class='caret'></span>";
            echo "</a>";

            echo "<ul class='dropdown-menu'>";
                if ($chartType == "rgraph") {
                    echo "<li><a href='?page=chart&charttype=highstock'>{$lang['Switch to']} Highstock</a></li>";
                    echo "<li><a href='?page=chart&charttype=highcharts'>{$lang['Switch to']} Highcharts</a></li>";
			  		echo "<li><a href='?page=chart&charttype=mergeCharts'>{$lang['Combine charts']}</a></li>";
	              	echo "<li><a href='?page=report'>{$lang['Report']} (RGraph)</a></li>";
                }
                elseif ($chartType == "highcharts") {
                    echo "<li><a href='?page=chart&charttype=rgraph'>{$lang['Switch to']} RGraph</a></li>";
                    echo "<li><a href='?page=chart&charttype=highstock'>{$lang['Switch to']} Highstock</a></li>";
			  		echo "<li><a href='?page=chart&charttype=mergeCharts'>{$lang['Combine charts']}</a></li>";
	              	echo "<li><a href='?page=report'>{$lang['Report']} (RGraph)</a></li>";
                }
                elseif ($chartType == "highstock") {
                    echo "<li><a href='?page=chart&charttype=rgraph'>{$lang['Switch to']} RGraph</a></li>";
                    echo "<li><a href='?page=chart&charttype=highcharts'>{$lang['Switch to']} Highcharts</a></li>";
			  		echo "<li><a href='?page=chart&charttype=mergeCharts'>{$lang['Combine charts']}</a></li>";
	              	echo "<li><a href='?page=report'>{$lang['Report']} (RGraph)</a></li>";
                }
                elseif ($chartType == "mergeCharts") {
                    echo "<li><a href='?page=chart&charttype=rgraph'>{$lang['Switch to']} RGraph</a></li>";
                    echo "<li><a href='?page=chart&charttype=highcharts'>{$lang['Switch to']} Highcharts</a></li>";
                    echo "<li><a href='?page=chart&charttype=highstock'>{$lang['Switch to']} Highstock</a></li>";
	              	echo "<li><a href='?page=report'>{$lang['Report']} (RGraph)</a></li>";
                }
                 elseif ($chartType == "report") {
                    echo "<li><a href='?page=chart&charttype=rgraph'>{$lang['Switch to']} RGraph</a></li>";
                    echo "<li><a href='?page=chart&charttype=highcharts'>{$lang['Switch to']} Highcharts</a></li>";
                    echo "<li><a href='?page=chart&charttype=highstock'>{$lang['Switch to']} Highstock</a></li>";
			  		echo "<li><a href='?page=chart&charttype=mergeCharts'>{$lang['Combine charts']}</a></li>";
                }
            echo "</ul>";
    echo "</div>";

    
    /* Include chart
    --------------------------------------------------------------------------- */
    if (!isset($_GET['view'])) {
        if ($chartType == "highcharts") {
            include("inc/chart_highchart.php");
        }
        elseif ($chartType == "highstock") {
            include("inc/chart_highstock.php");
        }
        elseif ($chartType == "mergeCharts") {
			include("inc/chart_merge.php");
        }
        elseif ($chartType == "rgraph") {
            include("inc/chart_rgraph.php");
        }
        else {
            echo "Something went wrong.. Could'n determine chart to display. Try selecting chart in your userprofile.";
        }
    }
?>
