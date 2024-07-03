{{-- Dialog New/Edit --}}
<form id="newedit" style="display: none">
    <div class="mb-3">
        <label for="editName" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="editName" name="name" required>
    </div>
    <div class="mb-3">
        <label for="editPhone" class="form-label">Teléfono</label>
        <input type="text" class="form-control" id="editPhone" name="phone" required>
    </div>
    <div class="mb-3">
        <label for="editEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="editEmail" name="email" required>
    </div>
</form>

<script>
    function showEdit(id) {
        fetch(`/laboratories/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error(response.status); }
            return response.json();
        })
        .then(data => {
            let form = document.getElementById("newedit");
            form.reset();
            form.querySelector("#editName").value = data.name;
            form.querySelector("#editPhone").value = data.phone;
            form.querySelector("#editEmail").value = data.email;
            $.dialog({
                title: 'Editar Datos del Laboratorio',
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
                    fetch(`/laboratories/update/${id}`, {
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
            $.notification({ title: 'Editar Datos', message: 'No se pudo obtener los datos del laboratorio.', type: 'danger', delay: 3000 });
        });
    }

    function showNew() {
        let form = document.getElementById("newedit");
        form.reset();
        $.dialog({
            title: 'Nuevo Datos - Laboratorio',
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
                fetch('/laboratories/store', {
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
                    $.notification({ title: 'Nuevo Datos', message: 'Creación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => {
                    dialogOperation('close');
                    $.notification({ title: 'Nuevo Datos', message: 'Creación Fallida.', type: 'danger', delay: 3000 });
                });
            }
        });
    }
</script>

{{-- Dialog Info --}}
<script>
    function showInfo(id) {
        fetch(`/laboratories/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error('Error: ' + response.status); }
            return response.json();
        })
        .then(data => {
            $.dialog({
                title: 'Información del Laboratorio',
                body: `
                    <table>
                        <tr><th style="padding-right: 2rem">Nombre</th><td>${data.name}</td></tr>
                        <tr><th style="padding-right: 2rem">Teléfono</th><td>${data.phone}</td></tr>
                        <tr><th style="padding-right: 2rem">Email</th><td>${data.email}</td></tr>
                    </table>`,
                buttons: { accept: false, cancel: true },
                buttonText: { cancel: 'Cerrar' }
            });
        })
        .catch(error => {
            $.notification({
                title: 'Error', 
                message: 'No se pudo obtener los datos del laboratorio.', 
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
            title: 'Eliminar Item',
            body: '<p>¿Está seguro que desea eliminar este ítem?</p>',
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
            onAccept: function(dialogOperation) {
                fetch(`/laboratories/destroy/${id}`, {
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
                    $.notification({ title: 'Eliminar Item', message: 'Eliminación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => { 
                    $.notification({ title: 'Eliminar Item', message: 'Eliminación Fallida.', type: 'danger', delay: 3000 });
                    dialogOperation('close');
                });
            }
        });
    }
</script>
