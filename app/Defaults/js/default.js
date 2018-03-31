/* 
 * define js to module
 */

$(document).ready(function(){
    //ajax example
    $("#test").click(function(){
        $.ajax({
            type    :   "POST",
            url     :   "define/url/in/routes/post/file",
            data    :   {
                content : "json" //define typeData
            },
            beforeSend:function(){},
            success:function(){},
            error:function(){}
        });
    });
});
