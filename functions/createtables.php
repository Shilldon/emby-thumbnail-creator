<?php
    #include "functions/checkformainimage.php"; 
    /*function to display all the series folders on the network drive to enable the user to 
    pick the appropriate series for which to create thumbnails*/
    function displayAllSeries($error) {
        global $config;

        #cycle through the folders on the drive and output a table with poster images for each series
        $col = 1;
        $series_count = 0;
        $series_table = "<table>";
        $series_folder = $config['series_folder'];
        foreach (glob($series_folder."/*", GLOB_ONLYDIR) as $filename)
        {
            #create a table width of 5 columns
            $series_count++;
            if($col==1) {
                $series_table .= "<tr>";
            }
            if($col<=5) {
                $series_table .= "<td>"; 
                #images are stored in the directory as either folder.jpg or poster.jpg - check to choose
                #the appropriate image
                $img = $filename."/folder.jpg";
                if(is_file($img) != TRUE) {
                    $img = $filename."/poster.jpg";    
                }            
            }
            #extract the name of the series and set as value for enable series selection 
            $series_table .= "<input type=\"button\" class=\"button-image\" style=\"background-image:url(functions/getimage.php?i=".urlencode($img).")\" value=\"".str_replace($series_folder,"",$filename)."\" name=\"choose_series\" >";

            $series_table .= "</td>";
            $col++;             
            if($col==6) {
                $series_table .= "</tr>";
                $col=1;
            }
        }   
        $series_table .= "</table>";

        echo $series_table;
    }

    /*function to display all the seasons within a specific series selected to enable the user
    to pick the appropriate season for which to create thumbnails*/
    function createSeasonsTable($series) {
        
        global $config;

        $base_folder = $config['series_folder'];
        $series_folder = $base_folder."".$series;
        #cycle through the folders in the series and output a table with poster images for each season
        $col = 1;
        $seasons_count = 0;
        $seasons_table = "<table>";

        /*the objective is for a thumbnail for each episode in the season to be created from a main image in the
        season folder. If there is no main image, alert the user to this. Check for the main image through $no_image*/
        $no_image = false;

        #compile array of all seasons in the series folder
        $seasons_folders = glob($series_folder."/*", GLOB_ONLYDIR);
        #each season contains a metadata folder, remove this from the array
        if (($key = array_search($series_folder."/metadata", $seasons_folders)) !== false) {
            unset($seasons_folders[$key]);
        }  
        foreach ($seasons_folders as $season)
        {
            /*create a table of images (4 columns wide), based on the folder image with a button underneath each
            season for the user to select to create thumbnails for that season. */
            $seasons_count++;
            if($col==1) {
                $seasons_table .= "<tr>";
            }
            if($col<=4) {
                $seasons_table .= "<td>"; 
                /*most season folders contain an image for the season in the format season[number]-poster.jpg
                if this is missing search for a folder.jpg image, if that is missing look for a poster.jpg image*/
                $img = $series_folder."/season0".$seasons_count."-poster.jpg";
                if(is_file($img) != TRUE) {
                    $img = $season."/folder.jpg";    
                }
                if(is_file($img) != TRUE) {
                    $img = $season."/poster.jpg";    
                }          
                $seasons_table .= '<img src="functions/getImage.php?i=' . urlencode($img) . '">';

                /*If the season does not contain a main
                image for the thumbnails alert the user by changing the button appearance*/
                if(checkForMainEpisodeImage($series,"Season ".$seasons_count)) {
                    $seasons_table .= "<input class=\"btn btn-primary\"  type=\"submit\" value=\"Season ".$seasons_count."\" name=\"season_number\" />";
                }
                else {
                    $seasons_table .= "<button class=\"btn btn-danger image-needed\" type=\"button\" value=\"Season ".$seasons_count."\">No Image</button>"; 
                    $no_image = true;   
                }
                $seasons_table .= "</td>";
                $col++;            
            }   
            if($col==5) {
                $seasons_table .= "</tr>";
                $col=1;
            }
        }   
        $seasons_table .= "</table>"; 
        echo $seasons_table;
    }    
?>