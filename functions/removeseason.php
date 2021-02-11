<?php
    /*Emby .nfo files, containing information about the episode contain a <season> tag
    if included Emby displays the episode number before the episode name in the gui.
    Removing the season number prevents emby displaying the episode number.
    THis function replaces the season number in the <season> tag with an empty string*/
    include "../globals.php";
    $base_drive = $GLOBALS['series_folder'];
    $series = $_POST["choose_series"];
    $season = $_POST["choose_season"];        
    $season_folder = $base_drive."/".$series."/".$series." ".$season;
    #get the list of episodes in the season selected by the user
    $episodes = glob($season_folder."/*", GLOB_ONLYDIR);

    #season folders contain a folder called metadata - remove this from array
    if (($key = array_search($season_folder."/metadata", $episodes)) !== false) {
        unset($episodes[$key]);
    }  
    echo implode($episodes);
    foreach ($episodes as $episode) {
        #locate and read the nfo file for each episode
        
        $nfo_file = file_get_contents($episode.".nfo");
        #replace the <season> tag with an empty tag to prevent emby naming each episode with the episode number
        $output = preg_replace('/<season>[0-9]+?<\/season>/', '<season></season>', $nfo_file);
        if(is_array($output)) {
            file_put_contents($episode.".nfo",$output);
        }
    }
?>