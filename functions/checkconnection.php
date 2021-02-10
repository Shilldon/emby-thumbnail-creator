<?php
    include "../globals.php"; 
    $base_drive = $GLOBALS['series_folder'];
    if(isset($_POST['check_connection'])) {
        //echo json_encode(array("connection_status"=>"connected"));
        

        if(is_dir($base_drive)) {
            echo json_encode(array("connection_status"=>"connected"));
        }
        else {
            echo json_encode(array("connection_status"=>"disconnected"));
        }
    }
?>