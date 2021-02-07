<?php

    /*function to display all the series folders on the network drive to enable the user to 
    pick the appropriate series for which to create thumbnails*/
    function displayAllSeries($error) {
        global $config;

        #cycle through the folders on the drive and output a table with poster images for each series
        $col = 1;
        $series_count = 0;
        $series_table = "<table>";
        $series_folder = $config['series_folder'];
#        $series_folder = "Z:/";
        foreach (glob($series_folder."*", GLOB_ONLYDIR) as $filename)
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
            $series_table .= "<input type=\"submit\" class=\"button-image\" style=\"background-image:url(functions/getimage.php?i=".urlencode($img).")\" value=\"".str_replace($series_folder,"",$filename)."\" name=\"choose_series\" >";

    #        $series_table .= "<input type=\"submit\" class=\"button-image\" style=\"background-image:url(getimage.php?i=".urlencode($img).")\" value=\"".str_replace($series_folder,"",$filename)."\" name=\"choose_series\" >";        
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
?>