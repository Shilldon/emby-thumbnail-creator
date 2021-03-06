<?php 
    require "config.php";
    include_once "globals.php";
    
    include "functions/createtables.php";
    include "functions/checkisseriesvalid.php";
    include "functions/createthumbnails.php";
    include "functions/addtexttoimage.php";

    // Map the drive
    $letter = $GLOBALS["letter"];
    $location = $GLOBALS["location"];
    system("net use ".$letter.": \\".$location." /persistent:no>nul 2>&1");
?>
<?php
##Main tables built here
    #user has selected a list of images to convert to thumbnails for the elected seaon
    if(isset($_POST['process_images'])) {
        $action = $_POST['process_images'];
        $series = $_POST["choose_series"];
        $season = $_POST["choose_season"];
        $image_array = $_POST["image_array"];
        $_POST = array();
        if($action == "create_thumbnails") {
            createThumbnails($season, $series, $image_array);
        }
        else {
            addTextToImage($season, $series, $image_array);    
            createTableOfImages($series, $season, $image_array);   
        }
        exit();
    }
    #user has clicked on a season poster - display the episodes within that season
    else if(isset($_POST['choose_season'])) {
        $series = $_POST["choose_series"];
        $season = $_POST["choose_season"];
        $_POST = array();
        createEpisodesTable($series, $season);
        exit();
    }
    #user has clicked on a series poster - display the seasons within that series
    else if(isset($_POST['choose_series'])) {
        $series = $_POST["choose_series"];
        $_POST = array();
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