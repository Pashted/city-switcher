(function ($) {

    function save_and_redirect(host, result) {

        $.ajax({
            type:     'POST',
            url:      '/include/city-switcher/index.php',
            data:     {method: 'save', result: result, page: window.location.href},
            dataType: 'json',
            complete: function (response) {
                // если домен города существует и мы не находимся на нем
                if (host && host !== window.location.origin) {
                    window.location.href = host + window.location.pathname + window.location.search;
                    // console.log(host + window.location.pathname + window.location.search);
                }
            }
        });

    }

    $(document).ready(function () {

        var switcher = $('.city-switcher'),
            modal_switch = $('#switch-city'),
            modal_ask = $('#ask-city');

        modal_switch.modal({show: false});

        // нужно ли автоматически спрашивать о текущем городе?
        if (modal_ask.data('ask') === 1) {

            // Ваш город Х?
            modal_ask.modal({show: true, backdrop: 'static'});

            var answers = modal_ask.find('.btn');

            // Да
            answers.eq(0).on({
                click: function () {
                    modal_ask.modal('hide');
                    var user_city_url = modal_ask.find('.user-city').data('href');

                    save_and_redirect(user_city_url ? 'http://' + user_city_url : '', 'Да');
                }
            });


            // Нет (выбрать другой город)
            answers.eq(1).on({
                click: function () {
                    modal_ask.modal('hide');
                    modal_switch.modal('show');
                }
            });

            modal_switch.find('a').on({
                click: function (e) {
                    e.preventDefault();
                    var obj = $(this);
                    save_and_redirect(obj.attr('href'), 'Нет, выбран другой - ' + obj.text());
                    modal_switch.modal('hide');
                }
            });
        }

        // ==========================================================================

        modal_switch.on({
            'show.bs.modal': function () {
                $('body').addClass('modal-open');
            }
        });

        switcher.on({
            click: function () {
                modal_switch.modal('show');
            }
        });

    });
}(jQuery));