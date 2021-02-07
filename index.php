<?php 
    require "config.php";
    include "functions/createtables.php"
?>
<?php echo file_get_contents("templates/header.html"); ?>
<?php echo file_get_contents("templates/body.html"); ?>
<?php 
##Main tables built here
    displayAllSeries("");
?>
<?php echo file_get_contents("templates/footer.html"); ?>