<?php
    include "../globals.php";
    $output = "";
    $result_code = 0;
    $connection_request = $_POST['connection_request'];
    if($connection_request == "disconnect") {
        exec("net use ".$series_folder." /delete /y", $output, $result_code); 
        if(!is_dir($series_folder)) {
            echo json_encode(array("connection_status"=>"disconnected"));
        }
    }
    else {
        exec("net use ".$letter.": \\".$location." /persistent:no>nul 2>&1");
        if(is_dir($series_folder)) {
            echo json_encode(array("connection_status"=>"connected"));
        }
    }
?>