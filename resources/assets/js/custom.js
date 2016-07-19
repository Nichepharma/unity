$(document).on({
    ajaxStart: function () {
        loader()
    },
    ajaxStop: function () {
        stopLoader();
    }
});

function loader(){
    $("body").addClass("loading");
}

function stopLoader(){
    $("body").removeClass("loading");
}

$(document).ready(function () {

    $("#searchicon").click(function () {
        $("#searchinput").toggle();
        $(".mainmenubg").toggleClass('mainmenubg-mob');
    });

    $(".mainmenubg").toggleClass('mainmenubg-mocab');


    $('#datetimepicker6').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#datetimepicker7').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false //Important! See issue #1075
    });
    $("#datetimepicker6").on("dp.change", function (e) {
        $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
        updateDays();
    });
    $("#datetimepicker7").on("dp.change", function (e) {
        $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
        updateDays();
    });


    $("#searchicon").click(function () {
        alert('searchicon')
        $("#searchinput").toggle();
        $(".mainmenubg").toggleClass('mainmenubg-mob');
    });

    $(".mainmenubg").toggleClass('mainmenubg-mob');
    $(".menu_mob_btn").click(function () {
        $(".menu").toggleClass('enaplemenu')
    });

    $("#notification").click(function () {
        $("#notificationmenu").toggle();
    });

    $("#support").click(function () {
        $("#supportmenu").toggle();
    });

    $('.buttonallsite3').on('click', function () {
        $('.buttonallsite3').removeClass('active');
        $(this).toggleClass('active');
    });

    $('.submenubuttons').on('click', function () {
        $('.submenubuttons').removeClass('active');
        $(this).toggleClass('active');
    });

    $('.page-head:empty').remove();

    setTimeout(function () {
        //$('.session_message').fadeTo( "slow", 0 );
        $('.session_message').slideUp("slow");
    }, 2000);

    $('.langSelect').on('change', function (event) {
        // change lang
        $("#switch_lang").submit();
    });


    if ($(".chosen").length) {
        $('.chosen').chosen({
            no_results_text: "No Data",
            allow_single_deselect: true
        });

    }
    if ($(".validate").length) {

        $(".validate").validate({
            ignore: [],
            rules: {
                email: {
                    maxlength: 50
                },
                mobile: {
                    maxlength: 15
                }
            }
        });
    }

});

function view_modal(url) {
    $.ajax({
        url: url
    }).done(function (data) {
        $("#my-modal-body").html(data);
//                  $('#my-modal').css('display','block');
        $('#my-modal').modal('show');
    }).fail(function () {
        generate_notification('information', "{{translate('main.you don\'t have permission')}}", {button: true});
    });
}

function alert_obj(obj) {
    var output = '';
    for (var key in obj) {
        output += key + ' : ' + obj[key] + '\n';
    }
    alert(output);
}

function alert_obj2(obj) {
    $.each(obj, function () {
        $.each(this, function (k, v) {
            alert(k + ' - ' + v);
        });
    });
}

function confirmDeleteRow(name, url) {
    $('#deleteForm').prop('action', url);
    $('#deletePageName').text(name);
    $('#modal-confirmDelete').modal({
        show: true
    });
}


//$(":file").filestyle({badge: false});
var weekday = new Array();
weekday[0] = "SUN";
weekday[1] = "MON";
weekday[2] = "TUE";
weekday[3] = "WED";
weekday[4] = "THU";
weekday[5] = "FRI";
weekday[6] = "SAT";

function updateDays() {
    var dF = document.getElementById("datefrom");
    var dT = document.getElementById("dateto");
    if (dF) {
        dF = dF.value;
        if (dF != "") {
            var dateF = new Date(dF);
            document.getElementById("dayfrom").innerHTML = weekday[dateF.getUTCDay()];
        }
    }
    if (dT) {
        dT = dT.value;
        if (dT != "") {
            var dateT = new Date(dT);
            document.getElementById("dayto").innerHTML = weekday[dateT.getUTCDay()];
        }
    }
}

updateDays();

function parseDate(input) {
    var parts = input.split('-');
    // new Date(year, month [, day [, hours[, minutes[, seconds[, ms]]]]])
    return new Date(parts[0], parts[1]-1, parts[2]); // Note: months are 0-based
}

