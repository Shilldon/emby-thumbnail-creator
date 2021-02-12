$(document).ready(function() {
    //Check if network drive is mapped
    $(window).on('load', function() {
        checkConnection();
    });

    //Navbar
    /*button function to request connect or deconnect from network drive*/
    $(".button-connection").on("click", function() {
        var connectionRequest = $(this).val();
        var connectionRequestData = $.parseJSON(`{ 
            "connection_request" : "${connectionRequest}"
        }`);
        updateConnection(connectionRequestData);        
    });

    /*button function to call remove series*/
    $(".button-removeseason").on("click", function() {
        console.log("clicked")
        var series = $("#series-selected").val();
        var season = $("#season-selected").val();
        var removeSeasonData = $.parseJSON(`{ 
            "choose_series" : "${series}", 
            "choose_season" : "${season}" 
        }`);
        removeSeason(removeSeasonData);
    });

    /*button function to go back to previous table*/
    $("body").on("click",".button-back", function() {
        var series = $("#series-selected").val();
        var tableData = $.parseJSON(`{ "choose_series" : "${series}"}`);
        submitTableData(tableData);           
    })

    //Main Series table
    //close alert button
    $("body").on("click",".close", function() {
        $(".alert").removeClass("show");
    });

    /*user selects a series from main episode list - create data (the name of the series) in order to 
    display the seasons within the series.*/
    //dynamically created button - bound to body tag to enable function to be run on click
    $("body").on("click",".choose-series", function() {
        //hide any alert that might already be displayed
        $(".alert").removeClass("show");
        var series = $(this).val();
        var tableData = $.parseJSON(`{ "choose_series" : "${series}"}`);
        submitTableData(tableData);
    });

    //Seasons table
    /*user selects a season from main series list - create data (the name of the series and season) in order to 
    display the episodes within the series.*/
    //dynamically created button - bound to body tag to enable function to be run on click
    $("body").on("click",".choose-season", function() {
        $(".button-removeseason").removeAttr("disabled");
        //hide any alert that might already be displayed
        $(".alert").removeClass("show");
        var series = $("#series-selected").val();
        var season = $(this).val();
        var tableData = $.parseJSON(`{ 
            "choose_series" : "${series}", 
            "choose_season" : "${season}" 
        }`);
        submitTableData(tableData);
    });

    //dynamically created button - bound to body tag to enable function to be run on click
    $("body").on("click",".image-needed", function() {
        if($(this).attr("data-selected")!="selected") {
            //deselect all buttons and revert their values
            $(".image-needed").removeAttr("data-selected");
            $(".image-needed").addClass("btn-danger");
            $(".image-needed").removeClass("btn-success");
            $(".image-needed").text("No Image");
            //select this button, change its value
            $(this).attr("data-selected","selected");
            $(this).addClass("btn-success");
            $(this).text("Selected");
            $(this).removeClass("btn-danger");
            //show the container to receive the image file selected by user
            $(".drop-area-container").removeClass("d-none");           
        }
        else {
            //on second click deselect and reset all buttons
            $(".image-needed").removeAttr("data-selected");
            $(".image-needed").addClass("btn-danger");
            $(".image-needed").removeClass("btn-success");
            $(".image-needed").text("No Image");
            //hide the image drop area
            $(".drop-area-container").addClass("d-none");  
        }
    });

    //File drop area
    //change the colour of the drop area when the file is dragged over it
    //so the user can clearly see the file is in the area
    $("#drop-area").on('dragenter', function (e){
        e.preventDefault();
        e.stopPropagation();
        $(this).css('background', '#67717b');
    });

    //revert colour of drop area when file dragged off area so user
    //can see the file is no longer in the drop area
    $("#drop-area").on("dragleave", function() {
        $(this).css('background', 'white');        
    });

    //prevent default action when file dragged over to enable file upload on drop
    $("#drop-area").on('dragover', function (e){
        e.preventDefault();
        e.stopPropagation();
    });

    //change background to show successful file drop and create form containing
    //data and file to post by ajax
    $("#drop-area").on('drop', function (e){
        $(".drop-area-container").css('background', '#D8F9D3');
        e.preventDefault();
        e.stopPropagation();
        var image = e.originalEvent.dataTransfer.files;
        var series = $('#series-selected').val();
        var season = $('.image-needed[data-selected="selected"]').val();
        createFormData(image,series,season);
    });

    //close button for drop-area
    $(".close-filedrop").on("click", function() {
        //hide the drop area
        $(".drop-area-container").addClass("d-none"); 
        //reset the buttons
        $(".image-needed").removeAttr("data-selected");
        $(".image-needed").addClass("btn-danger");
        $(".image-needed").removeClass("btn-success");
        $(".image-needed").text("No Image");                
    })
    //Episodes table
    //Select all/select none global tick box selection toggle
    //dynamically created buttons - bound to body tag to enable function to be run on click
    $("body").on("click","#image-selection", function() {
        if($(this).val()=='select-none'){
            $('input:checkbox').prop('checked',false);
            $(this).text("Select All");
            $(this).val('select-all');
        }
        else {
            $('input:checkbox').prop('checked', true);
            $(this).val('select-none');        
            $(this).text("Select None");
        }
    });

    //create an array of checked episodes to either create episode images from the template or
    //to convert existing episode images into thumbnails
    $("body").on("click",".process-images", function() {
        var imagesArray = [];
        var action = "";
        if($(this).attr("id")=="create-thumbnails") {
            action = "create_thumbnails";
        }
        else {
            action = "add_text";
        }
        var noImageAlert = false;
        /*if the episode does not have an existing image prevent it from being added
        to the array to create thumbnails to prevent the back end attempted to process
        an image that does not exist*/
        $(".episode-checkbox:checkbox:checked").each(function() {     
            if(action == "create_thumbnails") {       
                if(!$(this).hasClass("no-image")) {
                    imagesArray.push($(this).val());   
                }
                else {
                    noImageAlert = true;
                }
            }
            else {
                imagesArray.push($(this).val());       
            }
        });
        /*alert the user if they selected some episodes without images*/
        if(noImageAlert == true) {
            $(".alert-message").html("Warning: some episodes did not have images.<br>Thumbnails not created for those episodes.<br>Use 'create image' first.");
            $(".alert").addClass("show");   
        }         
        var series = $("#series-selected").val();
        var season = $("#season-selected").val();
        var tableData = $.parseJSON(`{ 
            "choose_series" : "${series}", 
            "choose_season" : "${season}" ,
            "process_images" : "${action}",
            "image_array" : "${imagesArray}"
        }`);
        /*if the user has not selected any images at all prevent the data from passing to back end*/
        if(imagesArray.length>0) {
            submitTableData(tableData);
        }
        else {
            $(".alert-message").html("No valid images selected.");
            $(".alert").addClass("show");    
            setTimeout(function() {
                $(".alert").removeClass("show");
            }, 3000);           
        }        
    });

    //display large version of episode image on click
    $("body").on("click",".episode-image", function() {
        var imageSrc = $(this).attr("src");
        var episodeNumber = $(this).attr("data-episode");
        $(".modal").attr("data-episode",`${episodeNumber}`);
        $(".large-episodeimage").attr("src",`${imageSrc}`);
        $(".modal").modal("show");
    });

    //on selecting the image in the modal - tick the corresponding checkbox
    $("#select-episode").on("click", function() {
        var episodeNumber = $(".modal").attr("data-episode");
        $(`#${episodeNumber}`).prop('checked', true);
        $(".modal").modal("hide");
    });
})

/*function to send request to backend to connect or disconnect network drive
and to change status icon in navbar to indicate whether the network drive is connected*/
function updateConnection(connectionRequestData) {
    $(".button-connection").text("Connecting");
    removeClassStartingWith($(".button-connection"), 'btn-outline-');
    $(".button-connection").addClass("btn-outline-warning");
    $(".connected-icon").css("color","yellow");
    $(".connected-icon").addClass("flashing-text");     
    $.ajax({
        type: "POST",
        url: "functions/updateconnection.php",
        data: connectionRequestData,
        datatype: "json",
        success: function(response){
            var connection = JSON.parse(response);
            if(connection.connection_status == "disconnected") {
                $(".button-connection").text("Connect");
                removeClassStartingWith($(".button-connection"), 'btn-outline-');
                $(".button-connection").addClass("btn-outline-success");
                $(".button-connection").val("connect");
                $(".connected-icon").removeClass("flashing-text");
                $(".connected-icon").css("color","grey");                      
            }
            else if(connection.connection_status == "connected") {
                $(".button-connection").text("Disconnect");
                $(".button-connection").val("disconnect");
                removeClassStartingWith($(".button-connection"), 'btn-outline-');
                $(".button-connection").addClass("btn-outline-danger");
                $(".connected-icon").removeClass("flashing-text");             
                $(".connected-icon").css("color","yellowgreen");
            }
        },
        error(xhr,status,error) {
            console.log('status : ' + status + " error "+error);    
        }
    });    
}

//function to call backend to cycle through nfo files in the current season folder and
//remove the <season> tag. This prevents emby displaying the episode number in front of the 
//episode name in the gui.
function removeSeason(removeSeasonData) {
    $.ajax({
        type: "POST",
        url: "functions/removeSeason.php",
        data: removeSeasonData,
        datatype: "json",
        success: function(response){
            $(".alert").addClass("show");
            $(".alert-message").text("Series season data removed.");
        },
        error(xhr,status,error) {
            console.log('status : ' + status + " error "+error);    
        }
    });    
}

/*function to poll the page to determine if the mapped network drive is connected.
If it is display green linked icon in navbar else display grey unlinked icon and change
the nav menu option to "connect"*/
function checkConnection() {
    setTimeout(function() {
        $.ajax({
            type: "POST",
            url: "functions/checkConnection.php",
            data: { "check_connection" : "true" },
            datatype: JSON,
            success: function(response) {
                var connection = JSON.parse(response);
                if(connection.connection_status == "connected") {
                    removeClassStartingWith($(".button-connection"), 'btn-outline-');
                    $(".button-connection").addClass("btn-outline-danger");
                    $(".button-connection").text("Disconnect");
                    $(".button-connection").val("disconnect");
                    $(".connected-icon").removeClass("flashing-text");
                    $(".connected-icon").css("color","yellowgreen");
                }
                else if(connection.connection_status == "disconnected"){
                    $(".button-connection").addClass("btn-outline-success");
                    $(".button-connection").text("Connect");
                    $(".button-connection").val("connect");
                    $(".connected-icon").css("color","grey");                   
                }
            },
            complete: checkConnection,
            timeout: 2000
        })
    }, 5000);
}

//function to submit data to backend in order to display new tables series/seaons/episodes
function submitTableData(tableData) 
{
    $.ajax({
        type: "POST",
        data: tableData,
        datatype: "json",
        success: function(data){
            $(".table-contents").html(data);
        },
        error(xhr,status,error) {
            console.log('status : ' + status + " error "+error);    
        }
    });
}

//function to create data form to post by Ajax following user submitting a new image to use as
//episode thumbnails
function createFormData(image, series, season)
{
    var formImage = new FormData();
    formImage.append('user_image', image[0],"Main Episode Image.jpg");
    formImage.append('series_to_display',series);
    formImage.append('season_number',season);
    uploadFormData(formImage);
}

//function to upload image to backend with relevant data to name and save file as default image
//for thumbnails for user selected season
function uploadFormData(formData) 
{
    $.ajax({
        url: "functions/uploadimage.php",
        type: "POST",
        data: formData,
        contentType:false,
        cache: false,
        processData: false,
        success: function(data){
            var dict = $.parseJSON(data);
            var season = dict.season;
            //locate button clicked to select image for upload
            var selectedButton = $('.image-needed[data-selected="selected"]');
            //change button value, text and attributesso it can now be used to process thumbnails
            selectedButton.val(season);
            selectedButton.text(season);
            selectedButton.addClass("btn-primary");
            selectedButton.addClass("choose-season");
            selectedButton.removeClass("btn-success");
            selectedButton.removeClass("image-needed");
            //hide the file drop area
            $(".drop-area-container").addClass("d-none");
        }
    });
}    

/*function to remove a class starting with a specific string*/
function removeClassStartingWith(node, begin) {
    node.removeClass (function (index, className) {
        return (className.match ( new RegExp("\\b"+begin+"\\S+", "g") ) || []).join(' ');
    });
}