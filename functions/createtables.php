<?php
    include "functions/checkformainimage.php"; 
    /*function to display all the series folders on the network drive to enable the user to 
    pick the appropriate series for which to create thumbnails*/
    function displayAllSeries($message) {

        #cycle through the folders on the drive and output a table with poster images for each series
        $col = 1;
        $series_count = 0;
        $series_table ="";

        #if a message variable has been passed display alert with the message
        if(!empty($message)) {
            echo "<script>
                    $('.alert-message').text('".$message."');
                    $('.alert').addClass('show');
                </script>";    
            #clear message after being displayed to prevent it being displayed on next table selected
            $message = "";        
        }
        $series_table .= "<table>";
        $series_folder = $GLOBALS['series_folder'];
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
            $series_table .= "<input type=\"button\" class=\"button-image choose-series\" style=\"background-image:url(functions/getimage.php?i=".urlencode($img).")\" value=\"".str_replace($series_folder."/","",$filename)."\" name=\"choose_series\" >";
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
        $base_drive = $GLOBALS['series_folder'];
        $series_folder = $base_drive."/".$series;
        #cycle through the folders in the series and output a table with poster images for each season
        $col = 1;
        $seasons_count = 0;
        $seasons_table = "<table>";
        $seasons_table .= '<input type="hidden" id="series-selected" value="'.$series.'">';
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
                $seasons_table .= "<tr >";
            }
            if($col<=4) {
                $seasons_table .= '<td>'; 
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
                    $seasons_table .= "<input class=\"btn btn-primary choose-season\"  type=\"button\" value=\"Season ".$seasons_count."\" />";
                }
                else {
                    $seasons_table .= "<button class=\"btn btn-danger image-needed\" type=\"button\" value=\"Season ".$seasons_count."\">No Image</button>"; 
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

    function createEpisodesTable($series, $seasonNumber) {
        $base_drive = $GLOBALS['series_folder'];
        $season_folder = $base_drive."/".$series."/".$series." ".$seasonNumber;
        $col = 1;
        $episode_count = 0;
        $episode_table = '<div class="row justify-content-center">';
        $episode_table .= "<table>";
        #$episode_table .= "<input type=\"hidden\" id=\"series_name\" name=\"series_name\" value=\"".$series."\">";
        #$episode_table .= "<input type=\"hidden\" id=\"season_number\" name=\"season_number\" value=\"".$seasonNumber."\">";
        #$episode_table .= "<input type=\"hidden\" id=\"thumbnail_array\" name=\"\" value=\"\">";
        $episodes = glob($season_folder."/*", GLOB_ONLYDIR);
        if (($key = array_search($season_folder."/metadata", $episodes)) !== false) {
            unset($episodes[$key]);
        }  
        if(empty($episodes)) {
            echo "No existing thumbnails<br>";
        }
        else {
            foreach ($episodes as $episode_name)
            {
                $episode_name = str_replace($season_folder."/","",$episode_name);
                $episode_count++;
                if($col==1) {
                    $episode_table .= "<tr>";
                }
                if($col<=5) {
                    $episode_table .= "<td>"; 
                    $img = $base_drive."/".$series."/metadata/Episode Images/".$episode_name.".jpg";
                    if(file_exists($img) != true) {
                        $image_name = preg_replace("/^\d{1,2}+\s+\W+\s+/", "", $episode_name);
                        $img = $base_drive."/".$series."/metadata/Episode Images/".$image_name.".jpg";    
                    }
                    if(file_exists($img) != true) {
                        $img = "./images/no image.jpg";    
                    }
                    $episode_table .= '<img class="episode-image" src="functions/getImage.php?i=' . urlencode($img) . '" data-episode="episode'.$episode_count.'">';
                    $episode_table .= "<input class=\"episode-checkbox\" type=\"checkbox\" checked id=\"episode".$episode_count."\" name=\"".$episode_name."\" value=\"".$episode_name."\">";
                    $episode_table .= "</td>";
                    $col++;            
                }   
                if($col==6) {
                    $episode_table .= "</tr>";
                    $col=1;
                }
            }  
        }
        $episode_table .= '</table>';
        $episode_table .= '<div class="row">';
        $episode_table .= '<div class="col-12 d-flex justify-content-center">';
        $episode_table .= "<div style=\"text-align:center\"><button class=\"btn btn-danger\" style=\"margin: 5px\" type=\"button\" id=\"image_selection\">Select None</button></div>";     
        $episode_table .= "<div style=\"text-align:center\"><button class=\"btn btn-success\" style=\"margin: 5px\" type=\"submit\" value=\"true\" id=\"images_to_convert\" name=\"images_to_convert\">Convert Images</button></div>";     
        $episode_table .= "<div style=\"text-align:center\"><button class=\"btn btn-success\" style=\"margin: 5px\" type=\"button\" id=\"create_thumbnails\">Create Thumbnails</button></div>";     

        echo $episode_table;
    }

?>