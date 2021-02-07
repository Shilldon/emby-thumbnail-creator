<?php 
    require "config.php";
    include "environment.php";
    include "functions/createtables.php";
    include "functions/checkformainimage.php";

    $location = getenv("SHARED_DRIVE");
    $letter = "Z";
    $config["series_folder"] = $letter.":";
    // Map the drive
    system("net use ".$letter.": \\".$location." /persistent:no>nul 2>&1");

?>
<?php
##Main tables built here
        
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
    displayAllSeries("");
?>
<?php echo file_get_contents("templates/footer.html"); ?>