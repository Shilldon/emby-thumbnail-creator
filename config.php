<?php
    $host = 'localhost';
    $location = getenv("SHARED_DRIVE");
    $letter = "Z";
    $config["series_folder"] = $letter.":/";
    // Map the drive
    system("net use ".$letter.": \\".$location." /persistent:no>nul 2>&1");
?>