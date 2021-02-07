<?php 
    require "config.php";
    include "environment.php";
    include "functions/createtables.php";
    $location = getenv("SHARED_DRIVE");
    $letter = "Z";
    $config["series_folder"] = $letter.":/";
    // Map the drive
    system("net use ".$letter.": \\".$location." /persistent:no>nul 2>&1");

?>
<?php echo file_get_contents("templates/header.html"); ?>
<?php echo file_get_contents("templates/body.html"); ?>
<?php 
##Main tables built here
    displayAllSeries("");
?>
<?php echo file_get_contents("templates/footer.html"); ?>