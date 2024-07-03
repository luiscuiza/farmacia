    
    <form id="infocustomer" style="display: none;">
        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="mb-3">
            <label for="dni_nit" class="form-label">DNI/NIT</label>
            <input type="text" class="form-control" id="dni_nit" name="dni_nit" required>
        </div>
    </form>
        
    <script>
        let previousValue;
        let confirmCallback;

        function storePreviousValue(input) {
            previousValue = input.value;
        }

        function updateSubtotal(input) {
            const quantity = input.value;
            const tr = input.closest('tr');
            const price = tr.getAttribute('data-price');
            const subtotalElement = tr.querySelector('.subtotal');
            const subtotal = quantity * price;
            subtotalElement.textContent = `${subtotal} Bs`;
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('#cart-table tbody tr').forEach(row => {
                const subtotal = row.querySelector('.subtotal');
                if (subtotal) {
                    total += parseFloat(subtotal.textContent);
                }
            });
            document.getElementById('cart-total').textContent = `${total} Bs`;
        }

        function checkAndUpdateCart(id, input) {
            const previousValue = input.getAttribute('data-previous-value');
            const quantity = input.value;

            if (quantity != previousValue) {
                fetch('{{ route("cart.update") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success && data.maxstock !== undefined) {
                        input.value = data.maxstock;
                        $.notification({
                            title: 'Stock Insuficiente',
                            message: `No hay suficiente stock. Stock disponible: ${data.maxstock} del producto.`,
                            type: 'warning',
                            delay: 3000
                        });
                    } else {
                        $.notification({
                            title: 'Actualizar Carrito',
                            message: data.message,
                            type: data.success ? 'success' : 'danger',
                            delay: 3000
                        });
                    }
                    input.setAttribute('data-previous-value', quantity);
                })
                .catch(error => {
                    console.error('Error:', error);
                    $.notification({
                        title: 'Error',
                        message: 'Ocurrió un error al intentar actualizar el carrito.',
                        type: 'danger',
                        delay: 3000
                    });
                });
            }
        }

        function confirmDeleteProduct(id) {
            $.dialog({
                title: 'Eliminar Producto',
                body: '<p>¿Está seguro que desea eliminar este producto?</p>',
                buttons: { accept: true, cancel: true },
                buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
                onAccept: function(dialogOperation) {
                    fetch(`/cart/remove`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: id
                        })
                    })
                    .then(response => {
                        if (!response.ok) { throw new Error(response.status); }
                        return response.json();
                    })
                    .then(data => {
                        dialogOperation('hide');
                        if (data.success) {
                            $.notification({ title: 'Eliminar Producto', message: data.message, type: 'success', delay: 1000 });
                        } else {
                            $.notification({ title: 'Error', message: data.message, type: 'danger', delay: 1000 });
                        }
                        setTimeout(() => { location.reload(); }, 1500);
                    })
                    .catch(error => { 
                        $.notification({ title: 'Eliminar Producto', message: 'Error al eliminar el producto.', type: 'danger', delay: 1000 });
                        dialogOperation('close');
                    });
                }
            });
        }

        function clearCart() {
            $.dialog({
                title: 'Vaciar Carrito',
                body: '<p>¿Está seguro que desea vaciar el carrito?</p>',
                buttons: { accept: true, cancel: true },
                buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
                onAccept: function(dialogOperation) {
                    fetch("{{ route('cart.clear') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) { throw new Error(response.status); }
                        return response.json();
                    })
                    .then(data => {
                        dialogOperation('hide');
                        if (data.success) {
                            $.notification({ title: 'Vaciar Carrito', message: 'Carrito vaciado exitosamente.', type: 'success', delay: 1000 });
                            setTimeout(() => { location.reload(); }, 1500);
                        } else {
                            $.notification({ title: 'Error', message: 'No se pudo vaciar el carrito.', type: 'danger', delay: 1000 });
                        }
                    })
                    .catch(error => { 
                        $.notification({ title: 'Error', message: 'Error al vaciar el carrito.', type: 'danger', delay: 1000 });
                        dialogOperation('close');
                    });
                }
            });
        }

        function confirmSell() {
            const form = document.getElementById('infocustomer');
            form.style.display = '';

            $.dialog({
                title: 'Información del Cliente',
                body: form,
                buttons: { accept: true, cancel: true },
                buttonText: { accept: 'Vender', cancel: 'Cancelar' },
                onAccept: function(dialogOperation) {
                    if (form.checkValidity()) {
                        const formData = new FormData(form);
                        let jsonData = {};
                        formData.forEach((value, key) => { jsonData[key] = value; });

                        fetch('{{ route("cart.sell") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(jsonData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            dialogOperation('hide');
                            if (data.success) {
                                $.notification({
                                    title: 'Venta Exitosa',
                                    message: 'La venta se completó exitosamente.',
                                    type: 'success',
                                    delay: 3000
                                });
                                setTimeout(() => { window.location.href = "{{ route('cart.index') }}"; }, 1500);
                            } else {
                                $.notification({
                                    title: 'Error',
                                    message: data.message || 'No se pudo completar la venta.',
                                    type: 'danger',
                                    delay: 3000
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            $.notification({
                                title: 'Error',
                                message: 'Ocurrió un error al intentar completar la venta.',
                                type: 'danger',
                                delay: 3000
                            });
                            dialogOperation('close');
                        });
                    } else {
                        form.reportValidity();
                    }
                },
                onClose: function() {
                    form.style.display = 'none';
                }
            });
        }
    </script>