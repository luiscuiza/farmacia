{{-- Dialog New/Edit --}}
<form id="newedit" style="display: none">
    <div class="mb-3">
        <label for="editName" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="editName" name="name" required>
    </div>
    <div class="mb-3">
        <label for="editLastname" class="form-label">Apellido</label>
        <input type="text" class="form-control" id="editLastname" name="lastname" required>
    </div>
    <div class="mb-3">
        <label for="editDniNit" class="form-label">DNI/NIT</label>
        <input type="text" class="form-control" id="editDniNit" name="dni_nit" required>
    </div>
</form>

<script>
    function showEdit(id) {
        fetch(`/customers/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error(response.status); }
            return response.json();
        })
        .then(data => {
            let form = document.getElementById("newedit");
            form.classList.remove('was-validated');
            form.reset();
            form.querySelector("#editName").value = data.name;
            form.querySelector("#editLastname").value = data.lastname;
            form.querySelector("#editDniNit").value = data.dni_nit;
            $.dialog({
                title: 'Editar Datos del Cliente',
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
                    fetch(`/customers/update/${id}`, {
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
            $.notification({ title: 'Editar Datos', message: 'No se pudo obtener los datos del cliente.', type: 'danger', delay: 3000 });
        });
    }

    function showNew() {
        let form = document.getElementById("newedit");
        form.classList.remove('was-validated');
        form.reset();
        $.dialog({
            title: 'Nuevo Cliente',
            body: form,
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Guardar', cancel: 'Cancelar' },
            onClose: function() {
                form.reset();
                form.classList.remove('was-validated');
            },
            onAccept: function(dialogOperation) {
                if (!form.checkValidity()) {
                    return;
                }
                form.classList.remove('was-validated');
                let formData = new FormData(form);
                let jsonData = {};
                formData.forEach((value, key) => { jsonData[key] = value; });
                fetch('/customers/store', {
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
                    $.notification({ title: 'Nuevo Cliente', message: 'Creación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => {
                    dialogOperation('close');
                    $.notification({ title: 'Nuevo Cliente', message: 'Creación Fallida.', type: 'danger', delay: 3000 });
                });
            }
        });
    }
</script>

{{-- Dialog Info --}}
<script>
    function showInfo(id) {
        fetch(`/customers/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error('Error: ' + response.status); }
            return response.json();
        })
        .then(data => {
            $.dialog({
                title: 'Información del Cliente',
                body: `
                    <table>
                        <tr><th style="padding-right: 2rem">Nombre</th><td>${data.name}</td></tr>
                        <tr><th style="padding-right: 2rem">Apellido</th><td>${data.lastname}</td></tr>
                        <tr><th style="padding-right: 2rem">DNI/NIT</th><td>${data.dni_nit}</td></tr>
                    </table>`,
                buttons: { accept: false, cancel: true },
                buttonText: { cancel: 'Cerrar' }
            });
        })
        .catch(error => {
            $.notification({
                title: 'Error', 
                message: 'No se pudo obtener los datos del cliente.', 
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
            title: 'Eliminar Cliente',
            body: '<p>¿Está seguro que desea eliminar este cliente?</p>',
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
            onAccept: function(dialogOperation) {
                fetch(`/customers/destroy/${id}`, {
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
                    $.notification({ title: 'Eliminar Cliente', message: 'Eliminación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => { 
                    $.notification({ title: 'Eliminar Cliente', message: 'Eliminación fallida.', type: 'danger', delay: 3000 });
                    dialogOperation('close');
                });
            }
        });
    }
</script>
