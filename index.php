<?php 
    require "config.php";
    include "environment.php";
    include "functions/createtables.php";
    include "functions/checkformainimage.php";
    include "functions/checkisseriesvalid.php";

    $location = getenv("SHARED_DRIVE");
    $letter = "Z";
    $config["series_folder"] = $letter.":";
    // Map the drive
    system("net use ".$letter.": \\".$location." /persistent:no>nul 2>&1");

?>
<?php
##Main tables built here
     
    #user has clicked on a series poster - display the seasons within that series
    if(isset($_POST['choose_series'])) {
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