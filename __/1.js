$(function () {

    var hash = document.location.hash;
    if (hash) {
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }

// Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        history.pushState(null, null, e.target.hash);
    });

    if ($('.has-error').length) {
        $('.has-error').each(function () {
            var tabId = '#' + $(this).closest('.tab-pane').attr('id');
            $('li a[href="' + tabId + '"] i').removeClass('hide');
        });

        var first = $('.has-error:first').closest('.tab-pane').attr('id');
        $('li a[href="#' + first + '"]').tab('show');
    }

    $('.date_start').parent().datetimepicker({
        "format": "YYYY-MM-DD HH:mm:ss",
        "locale": "ru",
        "allowInputToggle": true
    });
    $(".status_status_").select2({
        "allowClear": true,
        "placeholder": {"id": "", "text": " \u0421\u0442\u0430\u0442\u0443\u0441 \u043b\u0438\u0434\u0430:"}
    });
    $('.status_date_').parent().datetimepicker({
        "format": "YYYY-MM-DD HH:mm:ss",
        "locale": "ru",
        "allowInputToggle": true
    });
    $(".servicing").select2({
        "allowClear": true,
        "placeholder": {
            "id": "",
            "text": "\u0422\u0438\u043f \u043e\u0431\u0441\u043b\u0443\u0436\u0438\u0432\u0430\u043d\u0438\u044f:"
        }
    });
    $(".action").select2({
        "allowClear": true,
        "placeholder": {"id": "", "text": "\u0414\u0435\u0439\u0441\u0442\u0432\u0438\u0435:"}
    });
    $('.action_date').parent().datetimepicker({
        "format": "YYYY-MM-DD HH:mm:ss",
        "locale": "ru",
        "allowInputToggle": true
    });

    $('.customer_status_.la_checkbox').bootstrapSwitch({
        size: 'small',
        onText: 'Юридический',
        offText: 'Физизический',
        onColor: 'danger',
        offColor: 'success',
        onSwitchChange: function (event, state) {
            $(event.target).closest('.bootstrap-switch').next().val(state ? 'on' : 'off').change();
        }
    });

    $('.customer_phone_').inputmask({"mask": "+7 (999) 999 99 99"});
    $('.customer_phone_ext_').inputmask({"mask": "+7 (999) 999 99 99"});
    $(document).off('change', ".volume.pest");
    $(document).on('change', ".volume.pest", function () {
        var target = $(this).closest('.fields-group').find(".method");
        $.get("/api/getter/methods?q=" + this.value, function (data) {
            target.find("option").remove();
            $(target).select2({
                data: $.map(data, function (d) {
                    d.id = d.id;
                    d.text = d.text;
                    return d;
                })
            }).trigger('change');
        });
    });


    $(document).on('click', '.volume-form-add-table', function () {
// tpl ROOT
        var tableRoot = $(this).parents('table');
        var tpl = $(document).find('template.volume-tpl');
// parent NEST NR
        var rootNr = $(this).parents('.has-many-child').attr('rel');
// table TRs NR
        var tbody = $(this).parents('.has-many-table-volume').find('tbody');
        var indexRoot = $(tbody).find('tr').length;
        rootNr++;

        console.log('indexRoot', indexRoot);
        console.log('__LA_KEY__', 'new___LAA_KEY__');


        index++;

        var template = tpl.html().replace(/new___LAA_KEY__/g, 'new_' + indexRoot).replace(/__LA_KEY__/g, 'new_' + rootNr);

        $(this).parents('table').find('tbody').append(template);

        $(".volume.pest").select2({
            "allowClear": true,
            "placeholder": {
                "id": "",
                "text": "\u041f\u0440\u0435\u0434\u043c\u0435\u0442 \u0440\u0430\u0431\u043e\u0442:"
            }
        });
        $(".volume.method").select2({
            "allowClear": true,
            "placeholder": {"id": "", "text": "\u041c\u0435\u0442\u043e\u0434:"}
        });

        $('.volume.square:not(.initialized)')
            .addClass('initialized')
            .bootstrapNumber({
                upClass: 'success',
                downClass: 'primary',
                center: true
            });

        $(".volume.entity").select2({
            "allowClear": true,
            "placeholder": {
                "id": "",
                "text": "\u0415\u0434\u0438\u043d\u0438\u0446\u0430 \u043f\u043b\u043e\u0449\u0430\u0434\u0438:"
            }
        });
    });

    $(document).on('click', '.volume-form-remove-table', function () {

        $(this).closest('.has-many-volume-form').hide();
        $(this).closest('.has-many-volume-form').find('.fom-removed').val(1);
    });


    $(document).on('click', '.implement-form-add-table', function () {
// tpl ROOT
        var tableRoot = $(this).parents('table');
        var tpl = $(document).find('template.implement-tpl');
// parent NEST NR
        var rootNr = $(this).parents('.has-many-child').attr('rel');
// table TRs NR
        var tbody = $(this).parents('.has-many-table-implement').find('tbody');
        var indexRoot = $(tbody).find('tr').length;
        rootNr++;

        console.log('indexRoot', indexRoot);
        console.log('__LA_KEY__', 'new___LAA_KEY__');


        index++;

        var template = tpl.html().replace(/new___LAA_KEY__/g, 'new_' + indexRoot).replace(/__LA_KEY__/g, 'new_' + rootNr);

        $(this).parents('table').find('tbody').append(template);


        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        $('.choose_master').datepicker()
            .on('changeDate', function (ev) {
                $('.choose_master').datepicker('hide');
                window.buttonDate = formatDate(ev.date.valueOf());
                window.buttonAction = $(this).attr('rel');

            });
        $('.implement.start_date').parent().datetimepicker({
            "format": "YYYY-MM-DD HH:mm:ss",
            "locale": "ru",
            "allowInputToggle": true
        });
        $('.implement.end_date').parent().datetimepicker({
            "format": "YYYY-MM-DD HH:mm:ss",
            "locale": "ru",
            "allowInputToggle": true
        });
    });

    $(document).on('click', '.implement-form-remove-table', function () {

        $(this).closest('.has-many-implement-form').hide();
        $(this).closest('.has-many-implement-form').find('.fom-removed').val(1);
    });
    var index = 0;
    $(document).on('click', '.act-form-add', function () {


        var tRoot = $(this).parents('.has-many-act');

        var tpl = $(tRoot).find('template.act-tpl');

        index++;

        var template = tpl.html().replace(/__LA_KEY__/g, index);
        $(tRoot).find('.has-many-act-forms').append(template);


//    var cnt = $(this).parents('.has-many-parent').find('.has-many-child');//.length;
//    console.log(cnt);
//    $(this).parents('.has-many-child').attr('rel',cnt)

//    console.log(cnt);
    });

    $(document).on('click', '.act-form-remove', function () {
        $(this).closest('.has-many-act-form').hide();
        $(this).closest('.has-many-act-form').find('.fom-removed').val(1);
    });

    $(".volume.pest").select2({
        "allowClear": true,
        "placeholder": {"id": "", "text": "\u041f\u0440\u0435\u0434\u043c\u0435\u0442 \u0440\u0430\u0431\u043e\u0442:"}
    });
    $(".volume.method").select2({
        "allowClear": true,
        "placeholder": {"id": "", "text": "\u041c\u0435\u0442\u043e\u0434:"}
    });

    $('.volume.square:not(.initialized)')
        .addClass('initialized')
        .bootstrapNumber({
            upClass: 'success',
            downClass: 'primary',
            center: true
        });

    $(".volume.entity").select2({
        "allowClear": true,
        "placeholder": {
            "id": "",
            "text": "\u0415\u0434\u0438\u043d\u0438\u0446\u0430 \u043f\u043b\u043e\u0449\u0430\u0434\u0438:"
        }
    });
    $('.after-submit').iCheck({checkboxClass: 'icheckbox_minimal-blue'}).on('ifChecked', function () {
        $('.after-submit').not(this).iCheck('uncheck');
    });
});
