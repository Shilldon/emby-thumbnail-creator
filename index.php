<?php 
    require "config.php";
    include_once "globals.php";
    
    include "functions/createtables.php";
    include "functions/checkisseriesvalid.php";

        // Map the drive
    $letter = $GLOBALS["letter"];
    $location = $GLOBALS["location"];
    system("net use ".$letter.": \\".$location." /persistent:no>nul 2>&1");
?>
<?php
##Main tables built here
     
    #user has clicked on a series poster - display the episodes within that series
    if(isset($_POST['choose_season'])) {
        $series = $_POST["choose_series"];
        $season = $_POST["choose_season"];
        createEpisodesTable($series, $season);
        exit();
    }
    #user has clicked on a series poster - display the seasons within that series
    else if(isset($_POST['choose_series'])) {
        $series = $_POST["choose_series"];
        createSeasonsTable($series);
        exit();
    }
?>

<?php echo file_get_contents("templates/header.html"); ?>
<?php echo file_get_contents("templates/body.html"); ?>
<?php 
##build main page table here
    #user enters search term in NavBar - check if the series is located on the shared drive
    if(isset($_POST['search_series'])) {
        $series = $_POST["search_series"];
        checkSeriesIsValid($series);
    }
    else {
        displayAllSeries("");
    }
?>
<?php echo file_get_contents("templates/footer.html"); ?>