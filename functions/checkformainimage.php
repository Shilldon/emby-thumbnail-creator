<?php
    function checkForMainEpisodeImage($series, $season_number) {
        global $config;
        $base_folder = $config['series_folder'];
        $series_folder = $base_folder."".$series."/".$series." ".$season_number;
        if(file_exists($series_folder."/Main Episode Image.jpg")) {
            return true;    
        }    
        else {
            return false;
        }
    }
?>