$(document).ready(function () {
    $(document.body).on("change",".select2-hidden-accessible", function(){
        if (($(this).val() != null) && ($(this).val() != '')) {
            $(this).parent('div').find('button.edit_select').removeAttr('disabled');
        } else {
            $(this).parent('div').find('button.edit_select').attr('disabled', 'disabled');
        }
        var attr = $(this).attr('data-krajee-depdrop');
        if (typeof attr !== typeof undefined && attr !== false) {
            if ($(this).is(':disabled')) {
                $(this).parent('div').find('button.add_select').attr('disabled', 'disabled');
            } else {
                $(this).parent('div').find('button.add_select').removeAttr('disabled');
            }
        }
    });
    $('select').each(function () {
        if ($(this).val()) {
            $(this).parent('div').find('button.edit_select').removeAttr('disabled');
            $(this).parent('div').find('button.add_select').removeAttr('disabled');
        }
    });
    $(document.body).on('change', '.select-on-check-all', function () {
        $(".kv-row-checkbox").prop("checked", $(this).is(":checked")).trigger("change");
    });
    $(document.body).on('change', '.kv-row-checkbox', function () {
        $(this).closest("tr").toggleClass(gridSelectedRow);
    });
    if ($('#RelatedFormModal').length > 0) {
        $('#RelatedFormModal').relatedModal();
    }
})

function deleteMultiple(elm){
    var element = $(elm),
        keys=$("#" + gridViewKey + "-grid").yiiGridView('getSelectedRows'),
        url = element.attr('data-url');
    $.post(url,{pk : keys},
        function (){
            if (useModal != true) {
                $.pjax.reload({container: "#" + gridViewKey + "-pjax-container"});
            }
        }
    );
}
