<?php
    
    /*copy episode images from metadata into the season folder and relabel into
    filename recognised by emby*/
    function createThumbnails($season, $series, $episodes) {
        $base_drive = $GLOBALS["series_folder"];
        $thumbnails = explode(",",$episodes);
        $seasonFolder = $base_drive."/".$series."/".$series." ".$season;    
        $metadataFolder = $base_drive."/".$series."/metadata/Episode Images/";
        $episode_count = 0;
        foreach($thumbnails as $thumbnail) {
            $episode_count++;
            $base_image = $metadataFolder."".$thumbnail.".jpg";
            /*many images are already stored in the base folder as just the name of the episode
            without the episode number. Check for this and, if the image is not valid rename it
            to the new format before running copy image*/
            if(!is_file($base_image)) {
                $unnumbered_thumbnail = preg_replace("/^\d{1,2}+\s+\W+\s+/", "", $thumbnail);
                rename($metadataFolder."".$unnumbered_thumbnail.".jpg", $metadataFolder."".$thumbnail.".jpg");
            }
            runCopyImage($base_image,$seasonFolder,$thumbnail."-thumb");
        }  
        #return to main page and confirm task complete
        displayAllSeries("Thumbnails Created");
    }

    #image copying process achieved through shell cmd line nconvert app
    #credit - https://www.xnview.com/en/nconvert/

    function runCopyImage($base_image, $baseFolder, $filename) {
        shell_exec("nconvert -overwrite -o \"".$baseFolder."/".$filename.".jpg\" \"".$base_image."\"");
    }    
?>