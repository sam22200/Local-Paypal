$(document).ready(function() {

    $("form").submit(function(e) {
        var valeur = $("input[name='PBX_CMD']").val();

        $.ajax({
            url: "storescripts/orderMail.php",
            type: "get", //send it through get method
            data:{data:valeur},
            success: function(response) {
                        //alert(valeur);
            },
            error: function(jqXHR, textStatus, errorThrown) {
        $("#error").html(jqXHR.responseText);
            }
        });
        return;
    });
});