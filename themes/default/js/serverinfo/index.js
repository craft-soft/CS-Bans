(function($, window) {
    'use strict';
    var serverinfoUrl = $('table#servers').data('info-url');
    $(document).on("click", "table#servers tbody td:not(.action)", function(){
        $("#loading").show();
        $.post(
            $('table#servers').data('modal-url'),
            window.getCsrfParam() + '=' + window.getCsrfToken() + '&sid=' + $(this).parent('tr').data('server-id')
        ).done(function(response) {
            $("#loading").hide();
            $('div#ServerDetail .modal-body').html(response);
            $('#ServerDetail').modal('show');
        });
    });
    function reloadServers() {
        $.each($('table#servers tbody tr'), function() {
            var row = $(this);
            $.post(
                serverinfoUrl,
                window.getCsrfParam() + '=' + window.getCsrfToken() + '&server=' + row.data('server-id')
            ).done(function(response){
                if(!response) {
                    row.addClass('error');
                    row.find('td.hostname').html(row.data('server-hostname')+' <b>Не отвечает</b>');
                    row.find('td.mod').text('');
                    row.find('td.os').text('');
                    row.find('td.vac').text('');
                } else {
                    row.removeClass('error');
                    row.find('td.mod').html($('<img>', {
                        src: response.modimg,
                        title: response.game,
                        alt: response.game
                    }));
                    row.find('td.os').html($('<img>', {
                        src: response.osimg,
                        title: response.os,
                        alt: response.os
                    }));
                    row.find('td.vac').html($('<img>', {
                        src: response.vacimg
                    }));
                    row.find('td.hostname').text(response.name);
                    row.find('td.players').text(response.players + '/' + response.playersmax);
                    row.find('td.map').text(response.map);
                }
                row.removeClass('warning');
            });
        });
    }
    reloadServers();
    setInterval(reloadServers, 8000);
})(jQuery, this);