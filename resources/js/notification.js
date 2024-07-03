(function($) {
    $.notification = function(options) {
        var settings = $.extend({
            title: 'Notificación',
            message: 'Este es un mensaje de notificación.',
            type: 'info',
            iconic: true,
            delay: 5000,
            onClose: null
        }, options);

        var iconClass;
        switch (settings.type) {
            case 'info':
                iconClass = 'fa fa-info-circle';
                break;
            case 'warning':
                iconClass = 'fa fa-exclamation-triangle';
                break;
            case 'danger':
                iconClass = 'fa fa-times-circle';
                break;
            case 'success':
                iconClass = 'fa fa-check-circle';
                break;
            case 'alert':
                iconClass = '';
                break;
            default:
                iconClass = 'fa fa-info-circle';
                break;
        }

        var notification = $('<div class="notification alert-' + settings.type + '"></div>');
        var header = $('<div class="nt-header"></div>');
        var icon = (settings.iconic && iconClass) ? $('<i class="' + iconClass + '"></i>') : '';
        var title = $('<h5>' + settings.title + '</h5>');
        var closeButton = $('<button type="button"><i class="fa fa-close"></i></button>');
        var content = $('<div class="nt-content pt-2"><p>' + settings.message + '</p></div>');

        closeButton.on('click', function() {
            notification.fadeOut(300, function() {
                $(this).remove();
                if (typeof settings.onClose === 'function') {
                    settings.onClose();
                }
                updateNotificationPositions();
            });
        });

        header.append(icon).append(title).append(closeButton);
        notification.append(header).append(content);
        $('body').append(notification);

        function updateNotificationPositions() {
            var notifications = $('.notification');
            var offset = 4;
            notifications.each(function() {
                $(this).css('top', offset + 'rem');
                offset += $(this).outerHeight() / 16 + 1;
            });
        }

        updateNotificationPositions();

        notification.hide().fadeIn(300);

        if (settings.delay > 0) {
            setTimeout(function() {
                notification.fadeOut(300, function() {
                    $(this).remove();
                    if (typeof settings.onClose === 'function') {
                        settings.onClose();
                    }
                    updateNotificationPositions();
                });
            }, settings.delay);
        }
    };
}(jQuery));
