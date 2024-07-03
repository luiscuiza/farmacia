(function($) {
    $.dialog = function(options) {
        var settings = $.extend({
            body: '', // string or HTMLElement
            title: 'Modal Dialog',
            validated: false,
            scrollable: false,
            buttons: {
                accept: true,
                cancel: true
            },
            buttonText: {
                accept: 'Aceptar',
                cancel: 'Cancelar'
            },
            onAccept: null, // function (dialog) -> dialog('show'), dialog('hide'), dialog('close')
            onClose: function() {} // function to call when the dialog is closed
        }, options);

        var acceptButtonHtml = settings.buttons.accept ? `<button type="button" class="btn btn-primary" id="dynamicDialogButton">${settings.buttonText.accept}</button>` : '';
        var cancelButtonHtml = settings.buttons.cancel ? `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${settings.buttonText.cancel}</button>` : '';

        var footerHtml = (settings.buttons.accept || settings.buttons.cancel) ? `
            <div class="modal-footer">
                ${cancelButtonHtml}
                ${acceptButtonHtml}
            </div>` : '';

        var modalHtml = `
            <div class="modal fade" id="dynamicDialog" tabindex="-1" role="dialog">
                <div class="modal-dialog${settings.scrollable ? ' modal-dialog-scrollable' : ''}" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">${settings.title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body"></div>
                        ${footerHtml}
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHtml);

        var $dialog = $('#dynamicDialog');
        var $dialogButton = $('#dynamicDialogButton');

        var bootstrapModal = new bootstrap.Modal($dialog[0], { backdrop: 'static', keyboard: true });
        bootstrapModal.show();

        // Insertar el body del diálogo y manejar visibilidad
        var originalDisplay = '';
        if (settings.body instanceof HTMLElement) {
            originalDisplay = settings.body.style.display;
            settings.body.style.display = '';
            $('.modal-body', $dialog).append(settings.body);
        } else {
            $('.modal-body', $dialog).html(settings.body);
        }

        function modalOperation(operation) {
            switch (operation) {
                case 'close':
                    closeModal();
                    break;
                case 'hide':
                    hideModal();
                    break;
                case 'show':
                    showModal();
                    break;
                default:
                    console.warn('Invalid Dialog Operation [show, hide, close]:', operation);
            }
        }

        function closeModal() {
            bootstrapModal.hide();
            $dialog.on('hidden.bs.modal', function () {
                if (settings.body instanceof HTMLElement) {
                    settings.body.style.display = originalDisplay; // Restaurar estilo display
                    $('body').append(settings.body); // Reinsertar el HTMLElement en el DOM
                }
                $dialog.remove();
                if (typeof settings.onClose === 'function') {
                    settings.onClose(); // Llamar a la función onClose
                }
            });
        }

        function hideModal() {
            bootstrapModal.hide();
        }

        function showModal() {
            bootstrapModal.show();
        }

        if (settings.buttons.accept) {
            $dialogButton.on('click', function() {
                if (typeof settings.onAccept === 'function') {
                    settings.onAccept(modalOperation);
                } else {
                    closeModal();
                }
            });
        }

        $dialog.find('.btn-secondary, .btn-close').on('click', function() {
            $dialog.addClass('closing');
        });

        $dialog.on('hidden.bs.modal', function () {
            if ($dialog.hasClass('closing')) {
                if (settings.body instanceof HTMLElement) {
                    settings.body.style.display = originalDisplay; // Restaurar estilo display
                    $('body').append(settings.body); // Reinsertar el formulario en el DOM
                }
                $dialog.remove();
                if (typeof settings.onClose === 'function') {
                    settings.onClose(); // Llamar a la función onClose
                }
            }
        });
    };
}(jQuery));
