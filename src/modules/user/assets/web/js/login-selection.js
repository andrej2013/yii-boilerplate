/*
 * Copyright (c) 2016.
 * @author Nikola Tesic (nikolatesic@gmail.com)
 */

/**
 * Created by Nikola on 12/14/2016.
 */
$(document).ready(function(){
    $('.username-selection').on('click', function () {
        userId = $(this).attr('data-id');
        url = $(this).attr('data-url');
        $.ajax({
                url: url,
                type: 'POST',
                data: {'userId': userId},
                dataType: 'json'
            })
            .done(function(json) {
                if (json.success) {
                    location.reload();
                } else {
                    $('#error-message').removeClass('hidden');
                    $('#error-message').html(json.message)
                }
            })
            .fail(function (xhr, status, errorThrown ) {
                console.log('xhr', xhr);
                console.log('status', status);
                console.log('error', errorThrown);
            });
    });

});
