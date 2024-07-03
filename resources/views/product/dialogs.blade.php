@php
    $user = Auth::user();
@endphp

@if ($user->role == 'admin')
{{-- Dialog New/Edit --}}
<form id="newedit" style="display: none">
    <div class="mb-3">
        <label for="editName" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="editName" name="name" required>
    </div>
    <div class="mb-3">
        <label for="editPrice" class="form-label">Precio</label>
        <input type="text" class="form-control" id="editPrice" name="price" required>
    </div>
    <div class="mb-3">
        <label for="editBarcode" class="form-label">Código de barras</label>
        <input type="text" class="form-control" id="editBarcode" name="barcode" required>
    </div>
    <div class="mb-3">
        <label for="editDescription" class="form-label">Descripción</label>
        <textarea class="form-control" id="editDescription" name="description"></textarea>
    </div>
    <div class="mb-3">
        <label for="editLaboratory" class="form-label">Laboratorio</label>
        <select class="form-control" id="editLaboratory" name="laboratory_id">
            <option value="">Seleccionar Laboratorio</option>
            @foreach ($laboratories as $laboratory)
                <option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>
            @endforeach
        </select>
    </div>
</form>

<script>
    function showEdit(id) {
        fetch(`/products/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error(response.status); }
            return response.json();
        })
        .then(data => {
            let form = document.getElementById("newedit");
            form.reset();
            form.querySelector("#editName").value = data.name;
            form.querySelector("#editPrice").value = data.price;
            form.querySelector("#editBarcode").value = data.barcode;
            form.querySelector("#editDescription").value = data.description;
            form.querySelector("#editLaboratory").value = data.laboratory_id;
            $.dialog({
                title: 'Editar Datos del Producto',
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
                    fetch(`/products/update/${id}`, {
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
            $.notification({ title: 'Editar Datos', message: 'No se pudo obtener los datos del producto.', type: 'danger', delay: 3000 });
        });
    }

    function showNew() {
        let form = document.getElementById("newedit");
        form.reset();
        $.dialog({
            title: 'Nuevo Producto',
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
                fetch('/products/store', {
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
                    $.notification({ title: 'Nuevo Producto', message: 'Creación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => {
                    dialogOperation('close');
                    $.notification({ title: 'Nuevo Producto', message: 'Creación Fallida.', type: 'danger', delay: 3000 });
                });
            }
        });
    }
</script>
@endif

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
@if ($user->role == 'admin')
<script>
    function confirmDelete(id) {
        $.dialog({
            title: 'Eliminar Producto',
            body: '<p>¿Está seguro que desea eliminar este producto?</p>',
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
            onAccept: function(dialogOperation) {
                fetch(`/products/destroy/${id}`, {
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
                    $.notification({ title: 'Eliminar Producto', message: 'Eliminación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => { 
                    $.notification({ title: 'Eliminar Producto', message: 'Eliminación Fallida.', type: 'danger', delay: 3000 });
                    dialogOperation('close');
                });
            }
        });
    }
</script>
@endif