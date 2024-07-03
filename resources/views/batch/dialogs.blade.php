{{-- Dialog New/Edit --}}
<form id="newedit" style="display: none">
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

<script>
    
    function showEdit(id) {
        fetch(`/batches/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error(response.status); }
            return response.json();
        })
        .then(data => {
            let form = document.getElementById("newedit");
            form.reset();
            form.querySelector("#editCode").value = data.code;
            form.querySelector("#editQuantity").value = data.quantity;
            form.querySelector("#editStock").value = data.stock;
            form.querySelector("#editExpiration").value = data.expiration;
            form.querySelector("#editProduct").value = data.product_id;
            $.dialog({
                title: 'Editar Datos del Lote',
                body: form,
                buttons: { accept: true, cancel: true },
                buttonText: { accept: 'Guardar', cancel: 'Cancelar' },
                onClose: function() {
                    form.reset();
                },
                onAccept: function(dialogOperation) {
                    if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        return;
                    }
                    form.classList.remove('was-validated');
                    let formData = new FormData(form);
                    let jsonData = {};
                    formData.forEach((value, key) => { jsonData[key] = value; });
                    fetch(`/batches/update/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(jsonData)
                    })
                    .then(response => {
                        if (!response.ok) { throw new Error(response.status); }
                        return response.json();
                    })
                    .then(data => {
                        dialogOperation('hide');
                        $.notification({ title: 'Editar Datos', message: 'Actualización exitosa.', type: 'success', delay: 0 });
                        setTimeout(() => { location.reload(); }, 1500);
                    })
                    .catch(error => {
                        dialogOperation('close');
                        $.notification({ title: 'Editar Datos', message: 'Actualización Fallida.', type: 'danger', delay: 3000 });
                    });
                }
            });
        })
        .catch(error => {
            $.notification({ title: 'Editar Datos', message: 'No se pudo obtener los datos del lote.', type: 'danger', delay: 3000 });
        });
    }

    function showNew() {
        let form = document.getElementById("newedit");
        form.reset();
        $.dialog({
            title: 'Nuevo Lote',
            body: form,
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Guardar', cancel: 'Cancelar' },
            onClose: function() {
                form.reset();
            },
            onAccept: function(dialogOperation) {
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }
                form.classList.remove('was-validated');
                let formData = new FormData(form);
                let jsonData = {};
                formData.forEach((value, key) => { jsonData[key] = value; });
                fetch('/batches/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    if (!response.ok) { throw new Error(response.status); }
                    return response.json();
                })
                .then(data => {
                    dialogOperation('hide');
                    $.notification({ title: 'Nuevo Lote', message: 'Creación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => {
                    dialogOperation('close');
                    $.notification({ title: 'Nuevo Lote', message: 'Creación Fallida.', type: 'danger', delay: 3000 });
                });
            }
        });
    }
</script>

{{-- Dialog Info --}}
<script>
    function showInfo(id) {
        fetch(`/products/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error('Error: ' + response.status); }
            return response.json();
        })
        .then(data => {
            $.dialog({
                title: 'Información del Producto',
                body: `
                    <table>
                        <tr><th style="padding-right: 2rem">Nombre</th><td>${data.name}</td></tr>
                        <tr><th style="padding-right: 2rem">Precio</th><td>${data.price}</td></tr>
                        <tr><th style="padding-right: 2rem">Laboratorio</th><td>${data.laboratory ? data.laboratory.name : 'N/A'}</td></tr>
                        <tr><th style="padding-right: 2rem">Código de barras</th><td>${data.barcode}</td></tr>
                        <tr><th style="padding-right: 2rem">Descripción</th><td>${data.description}</td></tr>
                    </table>`,
                buttons: { accept: false, cancel: true },
                buttonText: { cancel: 'Cerrar' }
            });
        })
        .catch(error => {
            $.notification({
                title: 'Error',
                message: 'No se pudo obtener los datos del producto.',
                type: 'danger',
                delay: 3000
            });
        });
    }
</script>

{{-- Dialog Delete --}}
<script>
    function confirmDelete(id) {
        $.dialog({
            title: 'Eliminar Lote',
            body: '<p>¿Está seguro que desea eliminar este lote?</p>',
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
            onAccept: function(dialogOperation) {
                fetch(`/batches/destroy/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) { throw new Error(response.status); }
                    return response.json();
                })
                .then(data => {
                    dialogOperation('hide');
                    $.notification({ title: 'Eliminar Lote', message: 'Eliminación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => { 
                    $.notification({ title: 'Eliminar Lote', message: 'Eliminación Fallida.', type: 'danger', delay: 3000 });
                    dialogOperation('close');
                });
            }
        });
    }
</script>

















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