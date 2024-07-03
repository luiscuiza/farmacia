{{-- Dialog New/Edit --}}
<form id="newedit" style="display: none">
    <div class="mb-3">
        <label for="editName" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="editName" name="name" required>
    </div>
    <div class="mb-3">
        <label for="editEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="editEmail" name="email" required>
    </div>
    <div class="mb-3">
        <label for="editPassword" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="editPassword" name="password" required>
    </div>
    <div class="mb-3">
        <label for="editPasswordConfirmation" class="form-label">Confirmar Contraseña</label>
        <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation" required>
    </div>
    <div class="mb-3">
        <label for="editRole" class="form-label">Rol</label>
        <select class="form-control" id="editRole" name="role" required>
            <option value="admin">Admin</option>
            <option value="user" selected>User</option>
        </select>
    </div>
    <hr>
    <div class="mb-3">
        <label for="selectProfile" class="form-label">Perfil</label>
        <select class="form-control" id="selectProfile" name="profile_id">
            <option value="" selected>Seleccionar Perfil / Crear Perfil</option>
            @foreach($profiles as $profile)
                <option value="{{ $profile->id }}">{{ $profile->name }} {{ $profile->lastname }}</option>
            @endforeach
        </select>
    </div>
    <div id="newProfileFields" style="display: none;">
        <div class="mb-3">
            <label for="inputProfileName" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="inputProfileName" name="profile_name">
        </div>
        <div class="mb-3">
            <label for="inputProfileLastName" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="inputProfileLastName" name="profile_lastname">
        </div>
        <div class="mb-3">
            <label for="inputProfilePhone" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="inputProfilePhone" name="profile_phone">
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectProfile = document.getElementById('selectProfile');
        selectProfile.addEventListener('change', function() {
            const newProfileFields = document.getElementById('newProfileFields');
            if (this.value === "") {
                newProfileFields.style.display = 'block';
                document.getElementById('inputProfileName').required = true;
                document.getElementById('inputProfileLastName').required = true;
                document.getElementById('inputProfilePhone').required = true;
            } else {
                newProfileFields.style.display = 'none';
                document.getElementById('inputProfileName').required = false;
                document.getElementById('inputProfileLastName').required = false;
                document.getElementById('inputProfilePhone').required = false;
            }
        });
        selectProfile.dispatchEvent(new Event('change'));
    });

    function showEdit(id) {
        fetch(`/users/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            let form = document.getElementById("newedit");
            form.reset();
            form.querySelector("#editName").value = data.name;
            form.querySelector("#editEmail").value = data.email;
            form.querySelector("#editRole").value = data.role;
            form.querySelector("#selectProfile").value = data.profile_id || "";
            form.querySelector("#editPassword").disabled = true;
            form.querySelector("#editPassword").closest("div").style.display = 'none';
            form.querySelector("#editPasswordConfirmation").disabled = true;
            form.querySelector("#editPasswordConfirmation").closest("div").style.display = 'none';
            selectProfile.dispatchEvent(new Event('change'));
            $.dialog({
                title: 'Editar Usuario',
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
                    fetch(`/users/update/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(jsonData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        dialogOperation('hide');
                        $.notification({ title: 'Editar Usuario', message: 'Actualización exitosa.', type: 'success', delay: 0 });
                        setTimeout(() => { location.reload(); }, 1500);
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                        dialogOperation('close');
                        $.notification({ title: 'Editar Usuario', message: 'Actualización Fallida.', type: 'danger', delay: 3000 });
                    });
                }
            });
        })
        .catch(error => {
            $.notification({ title: 'Editar Usuario', message: 'No se pudo obtener los datos del usuario.', type: 'danger', delay: 3000 });
        });
    }

    function showNew() {
        let form = document.getElementById("newedit");
        form.reset();
        form.querySelector("#editPassword").disabled = false;
        form.querySelector("#editPassword").closest("div").style.display = '';
        form.querySelector("#editPasswordConfirmation").disabled = false;
        form.querySelector("#editPasswordConfirmation").closest("div").style.display = '';
        $.dialog({
            title: 'Nuevo Usuario',
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
                fetch('/users/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    dialogOperation('hide');
                    $.notification({ title: 'Nuevo Usuario', message: 'Creación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => {
                    dialogOperation('close');
                    $.notification({ title: 'Nuevo Usuario', message: 'Creación Fallida.', type: 'danger', delay: 3000 });
                });
            }
        });
    }
</script>

{{-- Dialog ResetPassword --}}

<form id="resetpass" style="display: none">
    <h2 class="text-center fw-bold"></h2>
    <div class="mb-3">
        <label for="resetPassword" class="form-label">Nueva Contraseña</label>
        <input type="password" class="form-control" id="resetPassword" name="password" required>
    </div>
    <div class="mb-3">
        <label for="resetPasswordConfirmation" class="form-label">Confirmar Nueva Contraseña</label>
        <input type="password" class="form-control" id="resetPasswordConfirmation" name="password_confirmation" required>
    </div>
</form>

<script>
    function resetPassword(id) {
        const user = document.querySelector(`#user-${id} td`).textContent;
        const form = document.getElementById('resetpass');
        form.querySelector("h2").innerHTML = `Usuario: ${user}`;
        form.reset();
        $.dialog({
            title: 'Restablecer Contraseña',
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
                const formData = new FormData(form);
                const jsonData = {};
                formData.forEach((value, key) => { jsonData[key] = value; });

                fetch(`/users/resetpassword/${id}`, {
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
                    $.notification({ title: 'Restablecer Contraseña', message: 'Contraseña actualizada exitosamente.', type: 'success', delay: 0 });
                    form.reset();
                })
                .catch(error => {
                    dialogOperation('close');
                    $.notification({ title: 'Restablecer Contraseña', message: 'Error al actualizar la contraseña.', type: 'danger', delay: 3000 });
                });
            }
        });
    }
</script>

{{-- Dialog Info --}}

<script>
    function showInfo(id) {
        fetch(`/users/${id}`)
        .then(response => {
            if (!response.ok) { 
                throw new Error('Error: ' + response.status); 
            }
            return response.json();
        })
        .then(data => {
            $.dialog({
                title: 'Información del Usuario',
                body: `
                    <table>
                        <tr><th style="padding-right: 2rem">Usuario</th><td>${data.name}</td></tr>
                        <tr><th style="padding-right: 2rem">Email</th><td>${data.email}</td></tr>
                        <tr><th style="padding-right: 2rem">Rol</th><td>${data.role}</td></tr>
                        <tr><th style="padding-right: 2rem">Nombre</th><td>${data.profile.name}</td></tr>
                        <tr><th style="padding-right: 2rem">Apellido</th><td>${data.profile.lastname}</td></tr>
                        <tr><th style="padding-right: 2rem">Telefono</th><td>${data.profile.phone ? data.profile.phone:''}</td></tr>
                    </table>`,
                buttons: { accept: false, cancel: true },
                buttonText: { cancel: 'Cerrar' }
            });
        })
        .catch(error => {
            $.notification({
                title: 'Error', 
                message: 'No se pudo obtener los datos del usuario.', 
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
            title: 'Eliminar Usuario',
            body: '<p>¿Está seguro que desea eliminar este usuario?</p>',
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
            onAccept: function(dialogOperation) {
                fetch(`/users/destroy/${id}`, {
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
                    $.notification({ title: 'Eliminar Usuario', message: 'Eliminación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => { 
                    $.notification({ title: 'Eliminar Usuario', message: 'Eliminación Fallida.', type: 'danger', delay: 3000 });
                    dialogOperation('close');
                });
            }
        });
    }
</script>
