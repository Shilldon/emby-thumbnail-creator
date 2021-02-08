$(document).ready(function() {
    //Main Series table
    /*user selects a series from main episode list - create data (the name of the series) in order to 
    display the seasons within the series.*/
    $('.choose-series').on("click", function() {
        //hide any alert that might already be displayed
        $(".alert").alert("close");
        var series = $(this).val();
        var tableData = $.parseJSON(`{ "choose_series" : "${series}"}`);
        submitTableData(tableData);
    });

    //Seasons table
    /*user selects a season from main series list - create data (the name of the series and season) in order to 
    display the episodes within the series.*/
    $("body").on("click",".choose-season", function() {
        //hide any alert that might already be displayed
        $(".alert").alert("close");
        var series = $("#series-selected").val();
        var season = $(this).val();
        var tableData = $.parseJSON(`{ 
            "choose_series" : "${series}", 
            "choose_season" : "${season}" 
        }`);
        submitTableData(tableData);
    });

    //dynamically created buttons - bound to body tag to enable function to be run on click
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

})

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
            selectedButton.attr("type","submit");
            selectedButton.addClass("btn-primary");
            selectedButton.removeClass("btn-success");
            selectedButton.removeClass("image-needed");
            //hide the file drop area
            $(".drop-area-container").addClass("d-none");
        }
    });
}    