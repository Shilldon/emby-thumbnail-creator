<?php
    #function to check input given by user matches a series on the network drive
    function checkSeriesIsValid($series) {
        global $config;
        $series_folder = $config['series_folder']."/".$series;
        #check if the directory exists
        if(is_dir($series_folder)) {
            createSeasonsTable($series);
        }
        else {
            displayAllSeries("Series not found.");
        }        
    }
?>