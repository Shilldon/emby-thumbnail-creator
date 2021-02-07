$(document).ready(function() {
    $('input[name="choose_series"]').on("click", function() {
        var series = $(this).val();
        var tableData = $.parseJSON(`{ "choose_series" : "${series}"}`);
        submitTableData(tableData);
    });
})

function submitTableData(tableData) 
{
    $.ajax({
        type: "POST",
        data: tableData,
        datatype: "json",
        success: function(data){
            console.log(data);
            $(".table-contents").html(data);
        },
        error(xhr,status,error) {
            console.log('status : ' + status + " error "+error);    
        }
    });
}