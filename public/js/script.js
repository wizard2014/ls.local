$(function() {
    // bg
    //(function() {
    //    var bg_img;
    //
    //    switch (screen.width) {
    //        case 1366:
    //            bg_img = '/img/misc/bg/bg_1366_768.jpg';
    //            break;
    //        case 1280:
    //            bg_img = '/img/misc/bg/bg_1280_720.jpg';
    //            break;
    //        default:
    //            bg_img = '/img/misc/bg/bg.jpg';
    //    }
    //
    //    $('body').css({
    //        'background' : '#1756E1 url("'+ bg_img +'") no-repeat'
    //    });
    //})();

    $('head').append('<link rel="stylesheet" href="/css/lightbox.min.css"><link rel="stylesheet" href="/fancybox/jquery.fancybox.min.css">');

    /******************************************************************************
     Page preloader
     *******************************************************************************/
    $('body').queryLoader2({
        barColor:        "#efefef",
        backgroundColor: "#1754E2",
        percentage:      true,
        barHeight:       1,
        minimumTime:     100,
        fadeOutTime:     500
    });

    // Прикрепить меню
    $('.pinned').pin({
        containerSelector: '.container',
        minWidth: 1170
    }).css({'z-index' : 9999});

    /******************************************************************************
     back to top
     *******************************************************************************/
    var back_to_top = $('#back-to-top');

    $(window).scroll(function () {
        if ($(this).scrollTop() > 350) {
            back_to_top.fadeIn();
        } else {
            back_to_top.fadeOut();
        }
    });

    // scroll body to 0px on click
    back_to_top.on('click', function() {
        $('#back-to-top').tooltip('hide');

        $('body, html').animate({
            scrollTop: 0
        }, 800);

        return false;
    });

    back_to_top.tooltip('show');

    /******************************************************************************
     fancybox
     *******************************************************************************/
    // Fires whenever a player has finished loading
    function onPlayerReady(event) {
        event.target.playVideo();
    }

    // Fires when the player's state changes.
    function onPlayerStateChange(event) {
        // Go to the next video after the current one is finished playing
        if (event.data === 0) {
            $.fancybox.next();
        }
    }

    // Initialise the fancyBox after the DOM is loaded
    var fancyYoutubeSettings = {
        maxWidth	: 800,
        maxHeight	: 600,
        fitToView	: false,
        width		: '70%',
        height		: '70%',
        autoSize	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none',
        beforeShow  : function() {
            // Find the iframe ID
            var id = $.fancybox.inner.find('iframe').attr('id');

            // Create video player object and add event listeners
            var player = new YT.Player(id, {
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }
    };

    //  $(".fancyboxPlay").fancybox(fancyYoutubeSettings);
    $(".fancyboxPlay").on('click', function(){
        var self = $(this);
        $.fancybox.open(self, fancyYoutubeSettings);
        return false;
    });

    /******************************************************************************
     tooltip
     *******************************************************************************/
    $('[data-toggle="tooltip"]').tooltip({animation: true});

    /******************************************************************************
     check form
     *******************************************************************************/
    var check_form = $('#check');

    check_form.find('span').addClass('sr-only');

    check_form.on('submit', function(e) {
        var flag = false,
            text_input = check_form.find('input[type="text"]');

        $.each(text_input, function() {
            if ($(this).val() != '') {
                flag = true;
            }
        });

        if (!flag) {
            e.preventDefault();

            if ($('#informer').length == 0) {
                $('.form').append('<p id="informer" class="text-danger text-center">Заполните хотя бы одно поле.</p>');
            }
        }
    });

    $('.name')
        .on('keypress', function(e) {
            return (/^[a-zA-z ]+$/.test(String.fromCharCode(e.charCode)));
        })
        .on('keyup', function(e) {
            if(e.keyCode == 13){
                $('[type="submit"]').trigger('click');
            }
        });

    $('.number')
        .on('keypress', function(e) {
            return !(/\D/.test(String.fromCharCode(e.charCode)));
        })
        .on('keyup', function(e) {
            if(e.keyCode == 13){
                $('[type="submit"]').trigger('click');
            }
        });

    /******************************************************************************
     email form
     *******************************************************************************/
    var email_form = $('#email-form');

    email_form.find('label').wrap('<div class="form-group"></div>');

    email_form.on('submit', function(e) {
        var reg   = /(?:\w)(?:(?:(?:(?:\+?3)?8\W{0,5})?0\W{0,5})?[34569]\s?\d[^\w,;(\+]{0,5})?\d\W{0,5}\d\W{0,5}\d\W{0,5}\d\W{0,5}\d\W{0,5}\d\W{0,5}\d(?!(\W?\d))/;
        var value = email_form.find('textarea').val();

        var phone_test = !reg.test(value),
            email_test = !/@/.test(value);

        if (email_test && phone_test) {
            e.preventDefault();

            if ($('#informer').length == 0) {
                $('.form').prepend('<p id="informer" class="text-danger text-center">Укажите контактные данные (телефон или email).</p>');
            }
        }
    });

    /******************************************************************************
     flash messager
     *******************************************************************************/
    var removeMsg = $('.remove');

    removeMsg.on('click', function() {
        $('.flash-messager').fadeOut('slow');
    });

    if (removeMsg.length > 0) {
        autoClose();
    }

    function autoClose() {
        setTimeout(function(){ removeMsg.trigger('click'); }, 8000);
    }

    /******************************************************************************
     check order
     *******************************************************************************/
    (function() {
        var $submitBtn  = $('#check').find('[type="submit"]'),
            $place      = $('.checked-data');

        $submitBtn.on('click', function(e) {
            e.preventDefault();

            var name    = $.trim($('.name').val()),
                number  = $.trim($('.number').val());

            if (name.length > 0 && !/\s/.test(name)) {
                $('.informer').remove();

                $('#check').after('<h4 class="text-center informer">Укажите "Имя" и "Фамилию".</h4>');

                return;
            }

            var data = {name: name, number: number};

            if (number != '' || name != '') {
                $.ajax({
                    type: 'POST',
                    url: '/',
                    data: data,
                    beforeSend: function() {
                        $submitBtn.prop('disabled', true).val('Обработка...');
                        $place.addClass('hide').find('tbody').empty();

                        $('.informer').remove();
                    }
                })
                    .done(function(data) {
                        if (data.result.length > 0) {
                            $.each(data.result, function() {
                                $place.find('tbody').append('<tr><td>' + $(this)[0] + ' ' + $(this)[1] + '</td><td>' + $(this)[2] + '</td><td><a href="http://cetatenie-primesc.eu/' + $(this)[4] + '" target="_blank">' + $(this)[3] + '</a></td><td>' + $(this)[5] + '</td></tr>');
                            });

                            $place.removeClass('hide');
                        } else {
                            $('#check').after('<h4 class="text-center informer">Таких данных нет.</h4>');
                        }
                    })
                    .fail(function() {
                        if ($('.informer').length < 1) {
                            $('#check').after('<h4 class="text-center informer">Произошла ошибка! Пожалуйста, повторите попытку позже.</h4>');
                        }
                    })
                    .always(function() {
                        $submitBtn.prop('disabled', false).val('Отправить');

                        $('.name').val('');
                        $('.number').val('');
                    });
            } else {
                if ($('.informer').length < 1) {
                    $('#check').after('<h4 class="text-center informer">Заполните хотя бы одно поле.</h4>');
                }
            }
        });
    })();
});