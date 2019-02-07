/*
 * Copyright (c) 2017.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by Nikola on 7/17/2017.
 */
var image = $('.qr-code-image'),
    alertBox = $('#info'),
    form = $('#login-form'),
    userField = $('input[name="TwoWay[user_id]"]');
    codeField = $('input[name="TwoWay[code]"]');
    refresh = $('#refreshLink');
    
var refreshCode = function(){
    $(refresh).on('click', function(event){
        event.stopPropagation();
        var link = $(this).attr('data-url');
        $.ajax({
            url: link,
            type: 'POST',
            data: {'user_id': user, 'code': code},
            dataType: 'json'
        })
        .done(function(json){
            $(alertBox).hide();
            $(image).attr('src', json.imageLink);
            $(form).attr('action', json.verifyLink);
            $(codeField).val(json.code);
            
            check();
        })
    });
    
};
var check = function () {
    url = $(form).attr('action'),
    
    $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json'
        })
        .done(function (json) {
            if (json.status == success) {
                window.location.href = json.returnUrl;
            } else if (json.status == expired) {
                var refreshDiv = '<a href="#" data-url="' + refreshLink + '" id="refreshLink">Refresh</a>';
                $(alertBox).html(json.message + '<br>' + refreshDiv);
                $(alertBox).show();
                refresh = $('#refreshLink');
                refreshCode();
            } else {
                setTimeout(check, 1000);
            }
        })
}

$(document).ready(function () {
    check();
});
