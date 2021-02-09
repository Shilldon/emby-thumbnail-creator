<?php
    
    /*copy episode images from metadata into the season folder and relabel into
    filename recognised by emby*/
    function createThumbnails($season, $series, $episodes) {
        $base_drive = $GLOBALS["series_folder"];
        $thumbnails = explode(",",$episodes);
        $season_folder = $base_drive."/".$series."/".$series." ".$season;    
        $metadata_folder = $base_drive."/".$series."/metadata/Episode Images/";
        $episode_count = 0;
        foreach($thumbnails as $thumbnail) {
            $episode_count++;
            $base_image = $metadata_folder."".$thumbnail.".jpg";
            /*many images are already stored in the base folder as just the name of the episode
            without the episode number. Check for this and, if the image is not valid rename it
            to the new format before running copy image*/
            if(!is_file($base_image)) {
                $unnumbered_thumbnail = preg_replace("/^\d{1,2}+\s+\W+\s+/", "", $thumbnail);
                rename($metadata_folder."".$unnumbered_thumbnail.".jpg", $metadataFolder."".$thumbnail.".jpg");
            }
            #image copying process achieved through shell cmd line nconvert app
            #credit - https://www.xnview.com/en/nconvert/
            #runCopyImage($base_image,$season_folder,$thumbnail."-thumb");
            shell_exec("nconvert -overwrite -o \"".$base_folder."/".$thumbnail."-thumb.jpg\" \"".$base_image."\"");
        }  
        #return to main page and confirm task complete
        displayAllSeries("Thumbnails Created");
    }

?>