{{-- Dialog New/Edit --}}
<form id="newedit" style="display: none">
    <div class="mb-3">
        <label for="editName" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="editName" name="name" required>
    </div>
    <div class="mb-3">
        <label for="editEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="editEmail" name="email" required>
    </div>
    <div class="mb-3">
        <label for="editPhone" class="form-label">Teléfono</label>
        <input type="text" class="form-control" id="editPhone" name="phone">
    </div>
    <div class="mb-3">
        <label for="editLaboratory" class="form-label">Laboratorio</label>
        <select class="form-control" id="editLaboratory" name="laboratory_id">
            <option value="">Ninguno</option>
            @foreach ($laboratories as $laboratory)
                <option value="{{ $laboratory->id }}">{{ $laboratory->name }}</option>
            @endforeach
        </select>
    </div>
</form>

<script>
    function showNew() {
        let form = document.getElementById("newedit");
        form.reset();
        $.dialog({
            title: 'Nuevo Proveedor',
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
                fetch('/suppliers/store', {
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
                    $.notification({ title: 'Nuevo Proveedor', message: 'Creación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => {
                    dialogOperation('close');
                    $.notification({ title: 'Nuevo Proveedor', message: 'Creación Fallida.', type: 'danger', delay: 3000 });
                });
            }
        });
    }

    function showEdit(id) {
        fetch(`/suppliers/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error(response.status); }
            return response.json();
        })
        .then(data => {
            let form = document.getElementById("newedit");
            form.reset();
            form.querySelector("#editName").value = data.name;
            form.querySelector("#editEmail").value = data.email;
            form.querySelector("#editPhone").value = data.phone;
            form.querySelector("#editLaboratory").value = data.laboratory_id;
            $.dialog({
                title: 'Editar Datos del Proveedor',
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
                    fetch(`/suppliers/update/${id}`, {
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
            $.notification({ title: 'Editar Datos', message: 'No se pudo obtener los datos del proveedor.', type: 'danger', delay: 3000 });
        });
    }
</script>

{{-- Dialog Info --}}
<script>
    function showInfo(id) {
        fetch(`/suppliers/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error('Error: ' + response.status); }
            return response.json();
        })
        .then(data => {
            $.dialog({
                title: 'Información del Proveedor',
                body: `
                    <table>
                        <tr><th style="padding-right: 2rem">Nombre</th><td>${data.name}</td></tr>
                        <tr><th style="padding-right: 2rem">Teléfono</th><td>${data.phone}</td></tr>
                        <tr><th style="padding-right: 2rem">Email</th><td>${data.email}</td></tr>
                        <tr><th style="padding-right: 2rem">Laboratorio</th><td>${data.laboratory ? data.laboratory.name : 'N/A'}</td></tr>
                    </table>`,
                buttons: { accept: false, cancel: true },
                buttonText: { cancel: 'Cerrar' }
            });
        })
        .catch(error => {
            $.notification({
                title: 'Error', 
                message: 'No se pudo obtener los datos del proveedor.', 
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
            title: 'Eliminar Proveedor',
            body: '<p>¿Está seguro que desea eliminar este proveedor?</p>',
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
            onAccept: function(dialogOperation) {
                fetch(`/suppliers/destroy/${id}`, {
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
                    $.notification({ title: 'Eliminar Proveedor', message: 'Eliminación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => { 
                    $.notification({ title: 'Eliminar Proveedor', message: 'Eliminación fallida.', type: 'danger', delay: 3000 });
                    dialogOperation('close');
                });
            }
        });
    }
</script>
