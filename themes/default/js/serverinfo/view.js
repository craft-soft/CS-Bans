(function($) {
    'use strict';
    $.contextMenu({
        selector: '.context-menu-one',
        callback: function(action, options) {
            var player = options.$trigger.attr('id');
            var reasontext;
            var reason = null;
            switch (action) {
                case 'ban':
                    reasontext = 'Забанить пользователя';
                    break;
                case 'kick':
                    reasontext = 'Кикнуть пользователя';
                    break;
                case 'message':
                    reasontext = 'Отправить сообщение пользователю';
                    break;
                default:
                    return false;
            }

            if(!confirm(reasontext + ' ' + player + '?')) {
                return false;
            }
            if(action == 'ban') {
                var reason = prompt('Введите причину бана', 'Читер');
                var bantime = prompt('Введите время бана в минутах (1440 - сутки, 10080 - неделя, 43200 - месяц)', '1440');
                if(!reason || !bantime) {
                    return false;
                }
            }

            if(action == 'message') {
                var reason = prompt('Введите сообщение для игрока ' + player, '');
                if(!reason) {
                    return false;
                }
            }
            $('#loading').show();
            $.post(
                $('meta[name=contextUrl]').attr('content'),
                {
                    action: action,
                    player: player,
                    reason: reason,
                    time: bantime
                }
            ).done(function(response) {
                if(response) {
                    alert(response);
                    $('#loading').hide();
                }
            });
        },
        items: {
            ban: {name: 'Забанить'},
            separator: '-----',
            kick: {name: 'Кикнуть'},
            separator2: '-----',
            message: {name: 'Отправить сообщение'}
        }
    });
})(jQuery);
