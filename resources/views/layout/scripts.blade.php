<script>


    @if(Session::has('message'))
    setTimeout(function () {

        generate_notification('{{Session::get('alert','error')}}', "{{ Session::get('message') }}", {
            dismissQueue: true,
            layout: 'topCenter',
            animation: {
                open: 'animated bounceInRight',
                close: 'animated bounceOutLeft'
            },
            timeout: 2000
        });


    }, 500);
    @endif



    @if ($errors->any())
    setTimeout(function () {
        @foreach($errors->all() as $error)
        generate_notification('error', '{{$error}}', {
            dismissQueue: true,
            layout: 'bottomRight',
            animation: {
                open: 'animated bounceInRight',
                close: 'animated bounceOutRight'
            }
        });
        @endforeach






    }, 200);
    @endif

    function generate_notification(type, text, more_options) {


        if (typeof(more_options) == 'undefined') more_options = {};

        var options = {
            text: text,
            type: type,
            layout: 'center',
            closeWith: ['click'],
            theme: 'relax',
            //            maxVisible: 1,
            //            killer: true,
            dismissQueue: false,
            animation: {
                open: 'animated shake',
                close: 'animated bounceOutUp',
                easing: 'swing',
                speed: 300
            }
        };

        $.each(more_options, function (option, value) {
            if (option == 'button')
                options['buttons'] = [{
                    addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {
                        $noty.close();
                    }
                }];
            else
                options[option] = value;
        });

        var n = noty(options);

        $(document).keyup(function (e) {
            if ($('.noty_bar').length > 0) {
                e = e || window.event;
                if (event.which == 13 || event.keyCode == 13 || e.keyCode == 27) {
                    n.close();
                    console.log(e.keyCode);
                }
            }

        });


    }

    function sure(message) {
        var n = noty({
            text: message,
            type: 'confirm',
            dismissQueue: false,
            layout: 'center',
            theme: 'relax',
            animation: {
                open: 'animated bounceInDown',
                close: 'animated bounceOutDown'
            },
            buttons: [
                {
                    addClass: 'btn btn-primary confirm_yes', text: '{{translate('main.yes')}}',
                    onClick: function ($noty) {
                        $noty.close();
                    }
                },
                {
                    addClass: 'btn btn-danger', text: '{{translate('main.no')}}',
                    onClick: function ($noty) {
                        $noty.close();
                    }
                }
            ]
        })
    }

    function getEqual(valType, valIndex) {
        if (valType == 'status_class') {
            var valArray = {0: 'danger', 1: 'success'};
        } else if (valType == 'status_toggle_class') {
            var valArray = {0: 'off', 1: 'on'};
        } else if (valType == 'fav_star') {
            var valArray = {0: 'fa-star-o', 1: 'fa-star'};
        } else if (valType == 'title') {
            var valArray = {{json_encode(Config::get(translate('main.titles_con')))}};
        } else if (valType == 'contact_type') {
            var valArray = {{json_encode(Config::get(translate('main.contact_types')))}};
        }
        if (valIndex in valArray) {
            return valArray[valIndex];
        } else {
            return valIndex;
        }
    }

    $(document).on('click', '.treeview-menu>li>a', function () {
        if (!window.event.ctrlKey) {
            $(this).find('i').attr('class', '').addClass('fa fa-spinner fa-spin');
        }

    });


    $(document).on('keypress', function (e) {

        if ($("input, textarea").is(":focus")) {
            return;
        }
        e = e || window.event;

        if (e.keyCode == '63' || e.keyCode == '47') {
            // shift (+) /
            window.location.href = '{{ url('/') }}';

        } else if (e.keyCode == '80') {
            // shift+p
            window.location.href = '{{ url('plan') }}';
        }
    });
</script>
