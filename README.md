EMBY Series Thumbnail Creator

## Brief
This website was created as a learning exercise for my first foray in to PHP coding.<br>
I am an avid user of EMBY open media solution (https://emby.media/) as a media server for my home network. There are various plug-ins available for EMBY but I wanted to build a custom app to enable me to generate images for the various TV series stored on a local network drive.<br>
EMBY automatically scans the media library and generates metadata for episodes in each series in a .nfo file. In addition EMBY creates a jpeg snap shot of a frame from each episode to use as a thumbnail in the GUI.<br>
While this is perfectly adequate I wanted to customise the images used for each episode and to automatically add text in various fonts and styles to each image. Given I have approximately 50 series on the network drive each containing several seasons each of which can contain up to 25 episodes this required an app to bulk edit.<br>
Initially I used XNview (https://www.xnview.com/en/), a very handy image editting tool to bulk add text to a chosen image and then manually updated the image metadata within EMBY. Not a swift solution!<br>
I then started to use Nconvert (https://www.xnview.com/en/nconvert/), the command line tool for XNview in batch files to bulk process images. Still not an idea solution. Nconvert was able to produce some but not all text effects I required for the images (e.g. placing a border round the text).<br> 
Enter this project.<br>

### Objectives
#### 1 - To provide a GUI to browse all series on the network drive;
#### 2 - To enable the user to upload a template image for each season within a series;
#### 3 - To generate an image for each episode which includes the name of the episode overlayed on the image in a specific font, style and position.

## User Stories
- I need to map the network drive in order to browse the series folder
- Save episode thumbnails in two locations; the metadata folder for the series, and within each season folder
- Retain the same directory structure as required by EMBY
- Choose which episode images I want to update rather than outputting all episodes in one go
- Review images before creating thumbnails
- Ensure the text in the image matches the name of the episode in the .nfo file, not the name of the directory (as with Xnview and Nconvert)
- Easily browse series using a GUI and not command line tools
- I need to be able to identify if a season does not already have a main template image
- I need to be able to identify if an episode does not already have a thumbnail associated with it
- I need to be able to bulk update the metadata for each episode to remove the \<season\> flag

## Purpose and Overview
This website is designed as a single page using ***PHP*** to generate content from ***HTML*** template header, body and footer files. The interface is designed to be as similar as possible to the GUI in the EMBY browserto achieve my aims as above.

### Colour Scheme and Typography
The dark green colour scheme was chosen to match the customised colour scheme in the EMBY browser.<br>
No change has been made to the standard browser font, again in keeping with the media browser used.<br>

### Technology overview
The technologies used are further set out below. In brief ***HTML*** was used to create templates for the top, middle and bottom of the single webpage. ***PHP*** functions executed from a single index.php file display the templates. Within the body template a table DOM element is updated with content generated through ***PHP***.<br>
***jQuery*** is used to provide the functionality of the buttons and to send information to the ***PHP*** backend using ***ajax*** commands.

### Navigation
The site is comprised of 4 levels:<br>
1 - List of series on network drive<br>
2 - List of seasons within a selected series<br>
3 - List of episode within a selected season<br>
4 - List of episode images generated that can be selected to generate thumbnails<br>
Level 1 is displayed on opening the page.<br>
Level 2 is displayed on selecting a series. A back button at the bottom of the page returns to the series view.<br>
Level 3 is displayed on selecting a season. A back button at the bottom of the page returns to the seasons view.<br>
Level 4 is displayed on selecting and processing the episodes for which to create episodes from the template. A back button at the bottom of the page returns to the complete list of episodes in the season.<br>

### How does it work?
#### Initialising
A PHP server needs to be set up and run in the local environment. I use ***XAMPP for Windows*** with ***Apache*** server. On opening the main page the local network drive is mapped using ***PowerShell*** through ***PHP*** command line. The details of the network drive are contained in a local environment.php file (not contained in this repository).<br>
***jQuery*** polling determines if the local network drive has been mapped successfully and a valid connection icon is displayed in the menubar. Failure to connect will display an error icon.<br>
#### Folder structure and browsing
For the EMBY server the network folders are in the following structure:<br>
```
[NETWORK DRIVE]
+-- Series
|   +-- [SERIES 1]
|   +-- [SERIES 2]
|   +-- [SERIES 3]
|   +-- [SERIES 4]...
        folder.jpg
        +-- metadata
            +-- Main Episode Images
        +-- [Season 1]
        +-- [Season 2]
        +-- [Season 3]
        +-- [Season 4]...
            Main Episode Image.jpg
            folder.jpg
            nfo files
            [episode-names]-thumb.jpg files
            +-- [Episode 1]
            +-- [Episode 2]
            +-- [Episode 3]
            +-- [Episode 4]...
```
At each level ***PHP*** cycles through the folders to generate a list of series/seasons/episodes to create a table in ***HTML*** format that is echoed to the body template and displayed.<br>
Depending on the level hidden input tags are generated to record the series and season selected. These tags are used by ***jQuery*** to pass the appropriate information through ***ajax*** commands to the back end to generate the appropriate table.<br>

#### Image processing
In the series view the main image for each series is obtained and displayed in a table. The main image for the series is saved either as 'folder.jpg' or 'poster.jpg' in the series folder.<br>
In the season view the main image for each season is saved either as 'folder.jpg' or 'season[##].jpg' in the series folder.<br>
Each episode image is stored in the series/metadata/Main Episode Images/ folder.<br>
Each thumbnail (as used by EMBY) is stored as [Episodename]-thumb.jpg in the season folder where the episode subfolder is contained.<br>
Using appropriate logical checks ***PHP*** retrieves the images and displays in ***HTML*** format using a 'getimage' function. If an image does not exist a blank placeholder image is displayed.<br>
The user can select, using check boxes, the episodes for which they want to create images. On selecting those episodes and clicking the 'create images' button a duplicate of the Main Episode Image.jpg is created, renamed with the name of the relevant episode subfolder and saved in the main metadata folder. The name of the episode to display over the image is retrieved from the relevant .nfo file and using ***PHP*** ***GDIMAGE*** library functions that text is overlayed on the image. The font, position, size, colour and features of the text for each series (and, if appropriate, season) are saved in "series_info.xml" within the resources folder (not contained in this repository).<br>
The metadata/Main Images Folder/ holds these images in case the user wishes to specifically edit individual images. Once the episode images are processed the table is updated with the processed images. The user can then select which of those images will be converted into thumbnails and saved within the season folder. Those thumbnails are used by EMBY in its browser.<br>
The copying of the template image/creating thumbnails is undertaken through ***PowerShell*** command line function of the ***Nconvert*** application.<br>

#### NFO Metadata update
Where nfo files contain the \<season\> tag this causes the episode name to be displayed with a preceding number indicating the number of the episode in the season. While convenient this is a feature I prefer to disable as the episodes are listed in order anyway.<br>
Bulk removal of the \<season\> tag is achieved through a 'remove season' option at the top of the page that is only enabled once a series and season have been selected.<br>
***PHP*** function searches each nfo file in the chosen season to text replace and remove the relevant tag resaving the editted file. NB a refresh to the metadata selecting the 'Scan for new/updated files' option is required in the EMBY GUI before the amended episode names are displayed.<br>
### Features
#### Existing features
- Images used for buttons enabling users to easily see which series/season/episodes is being selected
- Uniform design across all page updates/tables to avoid confusion
- Alert box at top of page displayed with feedback messages
- Polling check to determine if network drive is connected and error displayed if it is disconnected
- Spinners are displayed to provide feedback to the user that  action is being taken by the backend while image processing is taking place
- A search function is implemented to enable users to search for a specific series
- The option by using checkboxes, to select some of the episodes within a season for which to create images/thumbnails is available
- Image size of the template image is displayed to assist the user in determining the correct font size for the text overlay
#### Features to implement
- Autoscaling of font for text overlay based on image size
- A progress bar rather than spinner to provide detailed update on image copying process

### Deployment
The app is designed to be run locally and must be downloaded to the user's local machine.

#### Requirements
***PHP Server*** a PHP server is required to run on the local machine.<br>
A network drive containing media files with the directory structure set out above.<br>
A browser supporting ***HTML5*** and ***jQuery***.<br>
The EMBY media browser server and interface. (While not specifically required to run this application EMBY is needed to see the benefit of the bulk image and metadata processing).<br>

#### Local Deployment
- To clone from this repository using terminal command:
```
git clone https://github.com/Shilldon/emby-thumbnail-creator.git
```
- Select the "Clone or Download" button from the git repository to download the app as a zip-file.

Within the root directory create an environment.php file containing the following code:
```
<?php
    putenv('SHARED_DRIVE=\\["Name of network drive"]');
?>
```
Replace ["Name of network drive"] with the name of your local network drive.<br>
Create a folder within the root directory named "resources". Create a subfolder within this directory named "fonts".<br>
Populate the fonts directory with fonts in TTF format (font names to be in capitals).<br>
Create a "series_info.xml" within the resources directory containing the following code and tags:
```
<?xml version="1.0" encoding="UTF-8"?>
<dvds>
<series name="[Name of the series]">
    <font>[font name]</font>
    <font_size>[font size]</font_size>
    <y_offset>[vertical offset in pixels from centre of page]</y_offset>
    <x_offset>[horizontal offset in pixels from centre of page]</x_offset>
    <colour>[color of font in R,G,B values]</colour>
    <rectangle>[true or false to indicate if a border will be drawn round the episode name]</rectangle>
    <capitalise>[true or false to indicate if the episode name will be displayed in capitals]</capitalise>
    <max_width>[Size in pixels of the maximum width of the episode name over the image]</max_width>    
</series>
</dvds>
```
If a specific series requires different font colours for each season include the following code between the \<colour\> tags.
```
    <colour>
        <Season>[color of font in R,G,B values]</Season>
        <Season>[color of font in R,G,B values]</Season>
        <Season>[color of font in R,G,B values]</Season>
        <Season>[color of font in R,G,B values]</Season>
        ...                                            
    </colour>
```
One tag required for each season even if colours are the same.<br>

Start the local ***PHP*** server and open file "index.php".<br>

### Technologies used
#### Development
- VSCode 2019 - IDE for development
- GitHub - project repository
- Git - version control
#### Front end
- HTML5 - Base markup
- CSS3 - Base cascading stylesheets
- jQuery 3.5.1 - javascript functionality for front end interaction and polling
- Bootstrap 4.4.1 - layout and design
#### Backend
- PHP 8.0 - Back-end page rendering and image processing
#### Icons
- Fontawesome 5.15.2 - Icons

### Credits
#### EMBY - https://emby.media/ media server and browser
#### EMBY logo - https://emby.media/
#### nConvert - https://www.xnview.com/en/ command line image copy and convert 

