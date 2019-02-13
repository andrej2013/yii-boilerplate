$.fn.hasAttr = function (name) {
    return this.attr(name) !== undefined;
};

(function ($) {
    
    // Register add and edit clicks
    $('button.add_select, button.edit_select').on('click', function () {
        var update   = $(this).hasAttr('data-update');
        var owner_id = $(this).closest('div.select2-bootstrap-append').find('select').attr('id');
        var url      = $(this).attr('data-url');
        var model_id = $(this).closest('div.select2-bootstrap-append').find('select').val();
        var pk       = $(this).attr('data-pk');
        var modal    = $('#RelatedFormModal');
        $(modal).attr('data-update', update);
        $(modal).attr('data-select2', owner_id);
        $(modal).attr('data-url', url);
        $(modal).attr('data-model-id', model_id);
        $(modal).attr('data-pk', pk);
    });
    $.fn.relatedModal = function () {
        // this.selector  = settings.selector;
        this.selector      = '#' + $(this).attr('data-select2');
        var attr_to_remove = ['update', 'select2', 'url', 'model-id', 'pk'];
        var temp           = this;
        var self           = this;
            loadForm       = function () {
                return new Promise(function (resolve, reject) {
                    var url = self.attr('data-url');
                    if (self.attr('data-update') == 'true') {
                        url += '&' + self.attr('data-pk') + '=' + $('#' + self.attr('data-select2')).val();
                    }
                    $.get(url)
                        .done(function (data) {
                            $('.loader').addClass('hide');
                            resolve(data);
                        });
                })
            },
            submitForm     = function (form) {
                var select2 = '#' + self.attr('data-select2');
                return new Promise(function (resolve, reject) {
                    $('.loader').removeClass('hide');
                    $.post(
                        form.attr("action"),
                        form.serialize()
                        )
                        .done(function (data) {
                            if ($(select2).find("option[value=" + data.id + "]").length) {
                                // Update existing entry
                                var optionSelected      = $(select2).find("option:selected"),
                                    data_krajee_select2 = $(select2).attr('data-krajee-select2');
                                optionSelected.text(data.label).trigger("change");
                                // Destroy select2 widget and recreate using same config
                                $(select2).select2("destroy").select2(window[data_krajee_select2]);
                                $(select2).trigger('update');
                            } else {
                                // Create the DOM option that is pre-selected by default
                                var newState = new Option(data.label, data.id, true, true);
                                // Append it to the select
                                $(select2).val(data.id);
                                $(select2).append(newState).trigger('change');
                                $(select2).trigger('update');

                            }
                            $('.loader').addClass('hide');
                            resolve(data);
                        })
                        .fail(function (data) {
                            $('.loader').addClass('hide');
                            reject(data.responseJSON);
                        });
                })
            };

        $($(this).attr('href')).find('form').on('submit', function (e) {
            e.preventDefault();
        });
        $($(this).attr('href')).find('form').on('beforeSubmit', function (e) {
            submitForm($(this)).then(function () {
                removeTab(false);
            });
            return false;
        });
        return this
            .on('submit', 'form', function (e) {
                e.preventDefault();
            })
            .on('beforeSubmit', 'form', function (e) {
                submitForm($(this)).then(function (data) {
                    self.modal('hide');
                }, function (data) {
                    $(self).find('.modal-body').html(data);
                });
                
                return false;
            })
            .on('hide.bs.modal', function () {
                $(this).find('.modal-body').html('');
                for (var i in attr_to_remove) {
                    $(this).removeAttr('data-' + attr_to_remove[i]);
                }
                $(self).find('.modal-header').find('.header_title').remove();
            })
            .on('show.bs.modal', function (e) {
                // When modal is show load related form in it
                loadForm().then(function (data) {
                    $(self).find('.modal-body').html(data);
                    $(self).find('.modal-header').append($(self).find('.header_title').removeClass('hide'));
                });
            })
            ;
    };
}(jQuery));
