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
            $series_table.= "<script>
                    $('.alert-message').text('".$message."');
                    $('.alert').addClass('show');
                </script>";    
            #clear message after being displayed to prevent it being displayed on next table selected
            $message = "";        
        }
        $series_table = '<div class="row justify-content-center">';
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
        $series_table .= "</table></div>";

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
        $seasons_table = '<div class="row justify-content-center">';
        $seasons_table .= "<table>";
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
                #$seasons_table .= '<img src="functions/getImage.php?i=' . urlencode($img) . '">';

                /*If the season does not contain a main
                image for the thumbnails alert the user by changing the button appearance*/
                if(checkForMainEpisodeImage($series,"Season ".$seasons_count)) {
                    $seasons_table .= "<input type=\"button\" class=\"button-image choose-season\" style=\"background-image:url(functions/getimage.php?i=".urlencode($img).")\" value=\"Season ".$seasons_count."\">";
                    $seasons_table .= "<input class=\"btn btn-primary choose-season\"  type=\"button\" value=\"Season ".$seasons_count."\" />";
                }
                else {
                    $seasons_table .= "<input type=\"button\" class=\"button-image image-needed\" style=\"opacity: 0.4; background-image:url(functions/getimage.php?i=".urlencode($img).")\" value=\"Season ".$seasons_count."\">";
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
        $seasons_table .= "</table></div>"; 
        $seasons_table .= "<div style=\"text-align:center\"><a href=\"index.php\"><button class=\"btn btn-primary\" style=\"margin: 5px\" type=\"button\">Back</button></a></div>";   
        echo $seasons_table;
    }      

    /*function to display all the episodes within a specific season selected to enable the user
    to pick the appropriate episodes to create thumbnails for*/
    function createEpisodesTable($series, $season) {
        $base_drive = $GLOBALS['series_folder'];
        $season_folder = $base_drive."/".$series."/".$series." ".$season;
        #cycle through season folder and list all sub-folders
        $col = 1;
        $episode_count = 0;
        $episode_table = '<div class="row justify-content-center">';
        $episode_table .= "<table>";
        $episode_table .= "<input type=\"hidden\" id=\"series-selected\" value=\"".$series."\">";
        $episode_table .= "<input type=\"hidden\" id=\"season-selected\" value=\"".$season."\">";

        $episodes = glob($season_folder."/*", GLOB_ONLYDIR);
        #season folders contain a folder called metadata - remove this from array
        if (($key = array_search($season_folder."/metadata", $episodes)) !== false) {
            unset($episodes[$key]);
        }  
        if(empty($episodes)) {
            echo "No existing episodes<br>";
        }
        else {
            /*sort the episodes alphabetically and extract the episode name from the file name.
            Search through [Main Series Folder] -> [metadata] -> [Episode Images] to locate existing thumbnail 
            images.
            The episode folder names all start with the episode number. The images for many episodes are stored with filenames
            in the format [Episode Number] - [Episode Name].jpg but many omit the episode number from the file name.
            To locate the images both formats need to be checked.
            Where a thumbnail image does not exist, display a blank image.*/
            $episode_table .= "<tr>";
            $episode_table .= "<td>";
            $img = $base_drive."/".$series."/".$series." ".$season."/Main Episode Image.jpg";
            $img_size = getimagesize($img);
            $episode_table .= '<img src="functions/getImage.php?i=' . urlencode($img) . '">Base Image<br>'.$img_size[0]." x ".$img_size[1];
            $episode_table .= "</td>";
            $episode_table .= "</tr>";
            natsort($episodes);
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
                    $no_image = "";
                    if(file_exists($img) != true) {
                        $image_name = preg_replace("/^\d{1,2}+\s+\W+\s+/", "", $episode_name);
                        $img = $base_drive."/".$series."/metadata/Episode Images/".$image_name.".jpg";    
                    }
                    if(file_exists($img) != true) {
                        $img = "../assets/images/no_image.jpg";   
                        $no_image = "no-image"; 
                    }
                    $episode_table .= '<img class="episode-image" src="functions/getImage.php?i=' . urlencode($img) . '" data-episode="episode'.$episode_count.'">';
                    $episode_table .= "<input class=\"episode-checkbox ".$no_image."\" type=\"checkbox\" checked id=\"episode".$episode_count."\" value=\"".$episode_name."\">";
                    $episode_table .= "</td>";
                    $col++;            
                }   
                if($col==6) {
                    $episode_table .= "</tr>";
                    $col=1;
                }
            }  
        }
        $episode_table .= '</table></div>';
        //display options to select/deselect all images, create new images from the template Main Episode Image or generate thumbnails
        $episode_table .= '<div class="row">';
        $episode_table .= '<div class="col-12 d-flex justify-content-center thumbnail-button-options">';
        $episode_table .= "<div style=\"text-align:center\"><button class=\"btn btn-danger\" type=\"button\" value=\"select-none\" id=\"image-selection\">Select None</button></div>";     
        $episode_table .= "<div style=\"text-align:center\"><button class=\"btn btn-primary process-images\" type=\"button\" id=\"convert-images\">Create Images</button></div>";     
        $episode_table .= "<div style=\"text-align:center\"><button class=\"btn btn-success process-images\" type=\"button\" id=\"create-thumbnails\">Create Thumbnails</button></div>";     
        $episode_table .= "<div style=\"text-align:center\"><button class=\"btn btn-primary button-back\" style=\"margin: 5px\" type=\"button\">Back</button></div>";   

        echo $episode_table;
    }

    function createTableOfImages($series, $season, $episodes) {
        $base_drive = $GLOBALS['series_folder'];
        $season_folder = $base_drive."/".$series."/".$series." ".$season;
        $episodes = explode(",",$episodes);
        $col = 1;
        $episode_count = 0;
        $image_table = '<div class="col-12 d-flex justify-content-center">';
        $image_table .= "<table>";
        foreach ($episodes as $episode_name)
        {
            $episode_count++;
            if($col==1) {
                $image_table .= "<tr>";
            }
            if($col<=5) {
                $image_table .= "<td>"; 
                $img = $base_drive."/".$series."/metadata/Episode Images/".$episode_name.".jpg";
                $image_table .= '<img class="episode-image" src="functions/getImage.php?i=' . urlencode($img) . '">';
                $image_table .= "</td>";
                $col++;            
            }   
            if($col==6) {
                $image_table .= "</tr>";
                $col=1;
            }
        }  
        $image_table .= "</table></div>";     
        $image_table .= "<div style=\"text-align:center\"><button class=\"btn btn-success\" style=\"margin: 5px\" type=\"button\">Create Thumbnails</button></div>";     
        echo $image_table;        
    }



?>