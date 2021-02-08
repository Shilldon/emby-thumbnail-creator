<?php
    include "../globals.php"; 
    $base_drive = $GLOBALS["series_folder"];

    if(isset($_FILES)) 
    {
        if(is_uploaded_file($_FILES['user_image']['tmp_name'])) {
            $postdata = file_get_contents("php://input");
            $sourcePath = $_FILES['user_image']['tmp_name'];
            $series = $_POST["series_to_display"];
            $season = $_POST['season_number'];
            $targetPath = $base_drive."/".$series."/".$series." ".$season."/".$_FILES['user_image']['name'];
            if(move_uploaded_file($sourcePath,$targetPath)) {
                $arr = array("season" => $season);
                echo json_encode($arr);
            }
        }
    }
?>