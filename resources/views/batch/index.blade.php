<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Lotes') }}
            </h2>
            <button class="btn btn-success d-flex align-items-center" onclick="showNewBatch()">
                <i class="fa fa-plus me-2"></i> Nuevo Lote
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
                    <table class="table mt-3" id="batches-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th>Fecha de Expiración</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batches as $batch)
                                @php
                                    $isExpired = \Carbon\Carbon::parse($batch->expiration)->isPast();
                                    $isOutOfStock = $batch->stock == 0;
                                @endphp
                                <tr id="batch-{{ $batch->id }}" class="{{ $isOutOfStock ? 'table-secondary' : ($isExpired ? 'table-danger' : '') }}">
                                    <td>{{ $batch->product ? $batch->product->name : '' }}</td>
                                    <td>{{ $batch->stock }}</td>
                                    <td>{{ $batch->expiration }}</td>
                                    <td>
                                        <a class="btn btn-info" onclick="showBatchInfo({{ $batch->id }})">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a class="btn btn-warning" onclick="showEditBatch({{ $batch->id }})">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger" onclick="confirmDeleteBatch({{ $batch->id }})">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Eliminar Lote -->
    <div class="modal fade" id="confirmDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este lote?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="deleteButton">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Mostrar Lote -->
    <div class="modal fade" id="infoDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Información del Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="infoDialogContent">Cargando...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Editar/Crear Lote -->
    <div class="modal fade" id="editDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Editar Datos - Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBatchForm">
                        <div class="mb-3">
                            <label for="editCode" class="form-label">Código</label>
                            <input type="text" class="form-control" id="editCode" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="editQuantity" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="editStock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="editExpiration" class="form-label">Fecha de Expiración</label>
                            <input type="date" class="form-control" id="editExpiration" name="expiration" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProduct" class="form-label">Producto</label>
                            <select class="form-control" id="editProduct" name="product_id">
                                <option value="">Seleccionar Producto</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveEditButton">Guardar</button>
                </div>
            </div>
        </div>
</div>
    <!-- Scripts -->
    <script>
        /* Limpiador */
        document.addEventListener('DOMContentLoaded', function () {
            const quantityInput = document.getElementById('editQuantity');
            const stockInput = document.getElementById('editStock');

            quantityInput.addEventListener('blur', function () {
                if (parseInt(stockInput.value) > parseInt(quantityInput.value)) {
                    stockInput.value = quantityInput.value;
                }
            });

            stockInput.addEventListener('blur', function () {
                if (parseInt(stockInput.value) > parseInt(quantityInput.value)) {
                    stockInput.value = quantityInput.value;
                }
            });

            document.getElementById('infoDialog').addEventListener('hidden.bs.modal', function () {
                document.getElementById('infoDialogContent').innerHTML = "";
            });

            document.getElementById('editDialog').addEventListener('hidden.bs.modal', function () {
                document.getElementById('editBatchForm').reset();
                document.getElementById('saveEditButton').onclick = null;
            });
            
        });

        /* Mostrar Mensaje */
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

        /* Función eliminar lote */
        function confirmDeleteBatch(id) {
            const confirmDialog = new bootstrap.Modal(document.getElementById('confirmDialog'), { backdrop: 'static', keyboard: true });
            confirmDialog.show();
            document.getElementById('deleteButton').onclick = function () {
                confirmDialog.hide();
                fetch(`/batches/destroy/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    showMessage(data.success, data.message, true, 1000);
                })
                .catch(() => {
                    showMessage(false, 'Error al eliminar el lote.', false, 1000);
                });
            };
        }

        /* Función mostrar lote */
        function showBatchInfo(id) {
            fetch(`/batches/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('infoDialogContent').innerHTML = `
                        <p><strong>Producto:</strong> ${data.product ? data.product.name : 'N/A'}</p>
                        <p><strong>Código:</strong> ${data.code}</p>
                        <p><strong>Stock:</strong> ${data.stock}</p>
                        <p><strong>Cantidad:</strong> ${data.quantity}</p>
                        <p><strong>Fecha de Expiración:</strong> ${data.expiration}</p>
                    `;
                    const infoDialog = new bootstrap.Modal(document.getElementById('infoDialog'), { backdrop: 'static', keyboard: true });
                    infoDialog.show();
                })
                .catch(error => console.error('Error:', error));
        }

        /* Función editar lote */
        function showEditBatch(id) {
            fetch(`/batches/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editCode').value = data.code;
                    document.getElementById('editQuantity').value = data.quantity;
                    document.getElementById('editStock').value = data.stock;
                    document.getElementById('editExpiration').value = data.expiration;
                    document.getElementById('editProduct').value = data.product_id;
                    document.getElementById('modalTitle').innerText = 'Editar Datos - Lote';
                    document.getElementById('editProduct').readOnly = true;
                    document.getElementById('editCode').readOnly = true;
                    const editDialog = new bootstrap.Modal(document.getElementById('editDialog'), { backdrop: 'static', keyboard: true });
                    editDialog.show();
                    document.getElementById('saveEditButton').onclick = function () {
                        const formData = new FormData(document.getElementById('editBatchForm'));
                        fetch(`/batches/update/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            editDialog.hide();
                            showMessage(data.success, data.message, true, 1000);
                        })
                        .catch(error => console.error('Error:', error));
                    };
                })
                .catch(error => console.error('Error:', error));
        }

        /* Función nuevo lote */
        function showNewBatch() {
            document.getElementById('editCode').value = '';
            document.getElementById('editQuantity').value = '';
            document.getElementById('editStock').value = '';
            document.getElementById('editExpiration').value = '';
            document.getElementById('editProduct').value = '';
            document.getElementById('modalTitle').innerText = 'Nuevo Lote';
            const editDialog = new bootstrap.Modal(document.getElementById('editDialog'), { backdrop: 'static', keyboard: true });
            editDialog.show();
            document.getElementById('saveEditButton').onclick = function () {
                const formData = new FormData(document.getElementById('editBatchForm'));
                fetch(`/batches/store`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    editDialog.hide();
                    showMessage(data.success, data.message, true, 1000);
                })
                .catch(error => console.error('Error:', error));
            };
        }
    </script>
</x-app-layout>
