<?php
    function checkForMainEpisodeImage($series, $season_number) {
        $base_folder = $GLOBALS['series_folder'];
        $series_folder = $base_folder."".$series."/".$series." ".$season_number;
        if(file_exists($series_folder."/Main Episode Image.jpg")) {
            return true;    
        }    
        else {
            return false;
        }
    }
?>