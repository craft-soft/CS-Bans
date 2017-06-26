(function($) {
    'use strict';
    $(document).ready(function() {
        var serverinfoUrl = $('table#servers').data('info-url');
        $.each($('table#servers tbody tr'), function() {
            var row = $(this);
            $.post(
                serverinfoUrl,
                window.getCsrfParam() + '=' + window.getCsrfToken() + '&server=' + row.data('server-id'),
                function(response){
                    if(!response) {
                        row.addClass('error');
                        row.find('td.nick').html(row.data('server-hostname')+' <b>Не отвечает</b>');
                    } else {
                        row.find('td.nick').text(response.name);
                        row.find('td.players').text(response.players + '/' + response.playersmax);
                        row.find('td.map').text(response.map);
                    }
                    row.removeClass('warning');
                }
            );
        });
        $(document).on("click", "tr[data-url]", function() {
            document.location.href=$(this).data("url");
        });
    });
}) (jQuery);