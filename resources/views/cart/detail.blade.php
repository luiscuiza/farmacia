<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Carrito de Ventas') }}
            </h2>
            <button class="btn btn-danger d-flex align-items-center" onclick="clearCart()">
                <i class="fa fa-broom me-2"></i> Vaciar Carrito
            </button>
        </div>
    </x-slot>
    <!-- Contenido -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-10">
            <!-- Mensaje Success or Warning -->
            <div role="alert" id="messageBox" style="display: none;"></div>
            <!-- Contenedor -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container py-3">
                    <!-- Tabla -->
                    <table class="table mt-3" id="cart-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cart as $id => $details)
                                <tr id="cart-item-{{ $id }}" data-price="{{ $details['price'] }}">
                                    <td>{{ $details['name'] }}</td>
                                    <td>{{ $details['price'] }}</td>
                                    <td colspan="1">
                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control" style="width: 100px;" min="1" onfocus="storePreviousValue(this)" oninput="updateSubtotal(this)" onblur="checkAndUpdateCart({{ $id }}, this)">
                                    </td>
                                    <td class="subtotal">{{ $details['price'] * $details['quantity'] }}</td>
                                    <td style="width: 40px;">
                                        <button class="btn btn-danger" onclick="confirmDeleteProduct({{ $id }})"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">El carrito está vacío</td>
                                </tr>
                            @endforelse
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2" class="text-start"><strong id="cart-total">{{ session('total', 0) }} Bs</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    @if(count($cart) > 0)    
                        <button class="btn btn-success text-end" onclick="confirmSell()"><i class="fa fa-dollar-sign me-2"></i> Realizar Venta</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Cliente -->
    <div class="modal fade" id="infoCustomer" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Formulario de Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm" action="{{ route('cart.sell') }}" method="POST">
                        @csrf
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="submitCustomerForm()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Confirmacion -->
    <div class="modal fade" id="confirmDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage">¿Está seguro que desea realizar esta acción?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
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
                }).then(response => response.json())
                .then(data => {
                    if (!data.success && data.maxstock !== undefined) {
                        input.value = data.maxstock;
                        showMessage(data.success, `No hay suficiente stock. Stock disponible: ${data.maxstock} del producto.`, false);
                    } else {
                        showMessage(data.success, data.message, data.success);
                    }
                });
            }
        }

        function confirmDeleteProduct(id) {
            showConfirmDialog("¿Está seguro que desea eliminar este producto?", () => {
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
                .then(response => response.json())
                .then(data => {
                    showMessage(data.success, data.message, true, 1000);
                })
                .catch(() => {
                    showMessage(false, 'Error al eliminar el producto.', false, 1000);
                });
            });
        }

        function clearCart() {
            showConfirmDialog("¿Está seguro que desea vaciar el carrito?", () => {
                window.location = "{{ route('cart.clear') }}";
            });
        }

        function showConfirmDialog(message, callback) {
            const confirmDialog = new bootstrap.Modal(document.getElementById('confirmDialog'), { backdrop: 'static', keyboard: true });
            document.getElementById('confirmMessage').innerText = message;
            confirmCallback = callback;
            confirmDialog.show();
            document.getElementById('confirmButton').onclick = function () {
                confirmDialog.hide();
                confirmCallback();
            };
        }

        function showMessage(success, message, reload = false, ms = 1200) {
            const messageBox = document.getElementById('messageBox');
            messageBox.style.display = 'block';
            messageBox.innerText = message;
            messageBox.className = `alert ${success ? 'alert-success' : 'alert-danger'}`;
            if (reload) {
                setTimeout(() => { location.reload(); }, ms);
            } else {
                setTimeout(() => {
                    messageBox.innerText = '';
                    messageBox.style.display = 'none';
                }, ms);
            }
        }

        function confirmSell() {
            const infoDialog = new bootstrap.Modal(document.getElementById('infoCustomer'), { backdrop: 'static', keyboard: true });
	        infoDialog.show();
        }

        function submitCustomerForm() {
            document.getElementById('customerForm').submit();
        }
    </script>
</x-app-layout>
