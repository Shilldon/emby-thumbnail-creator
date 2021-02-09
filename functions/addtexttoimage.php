<?php
    /*function to create base images in metadata folder from which to create thumbnails.
    Image is first copied to metadata folder from the template image (Main Episode Image)
    Text taken from the name of the episode, contained in the nfo file, is then added to the image.
    Format of text for each series is contained in series_info.xml*/
    
    function addTextToImage($season, $series, $listOfImagesToConvert) {
        $base_drive = $GLOBALS["series_folder"];
        $season_folder = $base_drive."/".$series."/".$series." ".$season;   
        $base_image = $season_folder."/Main Episode Image.jpg";
        $metadata_folder = $base_drive."/".$series."/metadata/Episode Images";
        
        #get the list of episodes selected by the user
        $episodes = explode(",",$listOfImagesToConvert);
        foreach ($episodes as $episode) {
            #locate and read the nfo file which contains the true name of the series
            #(which is not, necessarily, the name of the nfo file or episode directory)
            $nfo_file = $season_folder."/".$episode.".nfo";
            $string = file_get_contents($nfo_file);
            $start_of_title = strpos($string, "<title>");
            #jump ahead in string by 7 characters - the length of "<title>"
            $start_of_title += 7; 
            #read to </title/> tag - this is the name of the episode
            $length_of_title = strpos($string, "</title>", $start_of_title) - $start_of_title;
            $episode_name = substr($string, $start_of_title, $length_of_title);
            #image copying process achieved through shell cmd line nconvert app
            #credit - https://www.xnview.com/en/nconvert/
            #create copy from Main Episode Image in metadata folder
            shell_exec("nconvert -overwrite -o \"".$metadata_folder."/".$episode.".jpg\" \"".$base_image."\"");
            #fun function to add text to the ima
            runAddText($series, $metadata_folder, $episode, $episode_name, $season);
        }
    }

    #function to add the episode name as text to the image
    function runAddText($series_name, $metadata_folder, $filename, $episode_name, $season) {

        //get the actual number of the season since $season_number is the string "Season [number]"
        $season_number = (int)str_replace("Season ","",$season);
    
        //get the blank base image created above
        $image_to_use = $metadata_folder."/".$filename.".jpg";
        $episode_base_image = imagecreatefromjpeg($image_to_use);

        //get the height and width of the base image to calculate position of text
        $image_width = imagesx($episode_base_image);  
        $image_height = imagesy($episode_base_image);

        //gat all the data about the series
        $series_data = getseries_data($series_name);

        //get the font        
        $font = $_SERVER['DOCUMENT_ROOT']."\\resources\\fonts\\".$series_data[0].".TTF";
        $font_size = (float)$series_data[1];
        
        #most series only have one font colour to use. Some have different colours for each season.
        #Check needed to see if more than one colour listed in series_info.xml
        if(count($series_data[4]) > 1)
        {
            $colors = explode(",",$series_data[4][$season_number-1]);
        }
        else {
            $colors = explode(",",$series_data[4][0]);           
        }
        
        $font_color = imagecolorallocate($episode_base_image, $colors[0], $colors[1], $colors[2]);
 
        #some episode titles on the image need to be in uppercase so convert to uppercase if appropriate
        if($series_data[6] == true) {
            $episode_name = strtoupper($episode_name);
        }

        #get the offset position values for the font on the image
        $yOffset = (float)$series_data[2];
        $xOffset = (float)$series_data[3];

        #get the maximum width the text can be on the page. If this is unspecified use the actual width of the image
        $max_width = (float)$series_data[7];
        if($max_width == 0) {
            $max_width = $image_width;
        }

        #get the bool to see if a rectangle is being placed round the text 
        $use_rectangle = $series_data[5];

        # Get bounding box size round text to calculate width of text to ensure it fits within the image
        $text_box = imagettfbbox($font_size,0,$font,$episode_name);

        #calculate text width and height
        $text_width = $text_box[2]-$text_box[0];
        $text_height = $text_box[1]-$text_box[7];    
        
        #if text width exceeds width of image then wrap the text
        if($text_width > $max_width) {
            #first get number of characters in the string
            $string_length = strlen($episode_name);
            #get number of characters per pixel
            $characters_per_pixel = $string_length/$text_width;
            #get number of characters that will fit across image (reduced to 80% of width of text_width to allow some
            #breathing space around the edge of the text. This will be the break point of the word wrap
            $character_break = intval($characters_per_pixel * ($max_width * 0.8), 10);
            $episode_name = wordwrap ($episode_name , $character_break , $break = "\n" , false);
            
            #split text into array of lines
            $lines = explode("\n", $episode_name);
            $number_of_lines = count($lines);
            #divide the lines by 2 so the middle line sits in the middle of the centered area
            $line_count = ($number_of_lines-1)/2.0;
            $full_text_height = $text_height * $number_of_lines;

            #calculate postition of each line and add each line to image
            $text_widths = array();
            foreach ($lines as $line) {
                $text_box = imagettfbbox($font_size,0,$font,$line);  
                $text_width = $text_box[2]-$text_box[0];  
                #store array of widths to get largest after iteration for applying rectangle (if necessary)
                $text_widths[] = $text_width;
                #Get starting co-ordinates of text to ensure text is centred
                #then offset by the amount specified in the series_info.xml
                #adjust y position based on line number
                $x = ($image_width/2) - ($text_width/2) + $xOffset;
                $y = ($image_height/2) + ($text_height/2) + $yOffset - ($line_count * $text_height * 1.2);      
                imagettftext($episode_base_image, $font_size, 0, $x, $y, $font_color, $font, $line); 
                $line_count = $line_count-1.0;
            }
            #recalculate y position and full height of the text for rectangle 
            #take widest width from array
            $text_width = max($text_widths);
            $x = ($image_width/2) - ($text_width/2) + $xOffset;
            $text_height = $full_text_height;
            $y = ($image_height/2) + ($full_text_height/2) + $yOffset;            
        }
        else {
            #Get starting co-ordinates of text to ensure text is centred
            #then offset by the amount specified in the series_info.xml
            $x = ($image_width/2) - ($text_width/2) + $xOffset;
            $y = ($image_height/2) + ($text_height/2) + $yOffset;

            #add the text
            imagettftext($episode_base_image, $font_size, 0, $x, $y, $font_color, $font, $episode_name);

        }
        
        if($use_rectangle == true) {
            #calculate positions for corners of rectangle around text 
            #40 pixel edge is given but this is based on image size of 1920 x 1080 - need to consider a percentage     
            $xLeft = $x-40;
            $xRight = $x + $text_width + 40 ;
            $yTop = $y-$text_height-30;
            $yBottom = $image_height - ($y-30 - $text_height);
            #add the rectangle  - set thickness of lines first
            imagesetthickness ($episode_base_image, 5);
            imagerectangle($episode_base_image, $xLeft, $yTop, $xRight , $yBottom, $font_color);
        }

        #output the image
        imagejpeg($episode_base_image, $metadata_folder."\\".$filename.".jpg");
        imagedestroy($episode_base_image);
    }

    #function to retrieve the font colour, position and type for the relevant series from xml file
    function getseries_data($series_name) {
        $xml=simplexml_load_file($_SERVER['DOCUMENT_ROOT']."\\resources\\series_info.xml") or die("Error: Cannot create object");

        $searched_name = $series_name;
        $number_of_series = count($xml->series);
        $i = 0;
        
        #cycle through xml to locate a series with the tag of the series name        
        while($xml->series[$i]['name'] != $searched_name && $i < $number_of_series){
            $i++;
        }
        
        #if the counter reaches the number of series in xml file the series wasn't found
        
        if($i == $number_of_series){
            echo 'series not found';
        }
        #otherwise i is the location of the relevant series in the xml file
        else
        {
            $font = $xml->series[$i]->font;
            $font_size = $xml->series[$i]->font_size;
            $y_offset = $xml->series[$i]->y_offset;
            $x_offset = $xml->series[$i]->x_offset;
            $number_of_font_colors = $xml->series[$i]->colour->Season->count();
            $color_array = array();        
            #some series have different font colours for each season so it is necessary to do a deeper search
            #on this element
            if($number_of_font_colors > 0) {
                foreach ($xml->series[$i]->colour->Season as $seasonFont) {
                    $color_array[] = $seasonFont;
                }              
            }
            else {
                $color_array[] = $xml->series[$i]->colour;
            }
            $use_rectangle = get_bool((string)$xml->series[$i]->rectangle);
            $use_capitals = get_bool((string)$xml->series[$i]->capitalise);
            $max_width = $xml->series[$i]->max_width;
        }
        return array ($font,$font_size,$y_offset, $x_offset, $color_array, $use_rectangle, $use_capitals, $max_width);
    }

    #simple function to return a text value of true or false as regular boolean value
    function get_bool($value){
        switch( strtolower($value) ){
            case 'true': return true;
            case 'false': return false;
            default: return NULL;
        }
    }     
    
?>