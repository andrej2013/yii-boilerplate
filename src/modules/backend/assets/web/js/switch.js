$(document).ready(function () {
    $('select.switch').on('change', function () {
        $.post(
            $(this).attr('data-url'),
            {
                id: $(this).val()
            },
            function () {
                location.reload();
            }
        )
    });
});
