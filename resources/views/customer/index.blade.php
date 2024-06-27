<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Clientes') }}
            </h2>
            <button class="btn btn-success d-flex align-items-center" onclick="showNew()">
                <i class="fa fa-plus me-2"></i> Nuevo Registro
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
                    <table class="table mt-3" id="customers-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>DNI/NIT</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr id="customer-{{ $customer->id }}">
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->lastname }}</td>
                                    <td>{{ $customer->dni_nit }}</td>
                                    <td>
                                        <button class="btn btn-info" onclick="showInfo({{ $customer->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning" onclick="showEdit({{ $customer->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="confirmDelete({{ $customer->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Eliminar Cliente -->
    <div class="modal fade" id="confirmDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este cliente?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="deleteButton">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Mostrar Cliente -->
    <div class="modal fade" id="infoDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Información del Cliente</h5>
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
    <!-- Modal Editar/Crear Cliente -->
    <div class="modal fade" id="editDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Editar Datos - Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerForm">
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
            document.getElementById('infoDialog').addEventListener('hidden.bs.modal', function () {
                document.getElementById('infoDialogContent').innerHTML = "";
            });
            document.getElementById('editDialog').addEventListener('hidden.bs.modal', function () {
                document.getElementById('editCustomerForm').reset();
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

        /* Función eliminar cliente */
        function confirmDelete(id) {
            const confirmDialog = new bootstrap.Modal(document.getElementById('confirmDialog'), { backdrop: 'static', keyboard: true });
            confirmDialog.show();
            document.getElementById('deleteButton').onclick = function () {
                confirmDialog.hide();
                fetch(`/customers/destroy/${id}`, {
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
                    showMessage(false, 'Error al eliminar el cliente.', false, 1000);
                });
            };
        }

        /* Función mostrar cliente */
        function showInfo(id) {
            fetch(`/customers/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('infoDialogContent').innerHTML = `
                        <p><strong>Nombre:</strong> ${data.name}</p>
                        <p><strong>Apellido:</strong> ${data.lastname}</p>
                        <p><strong>DNI/NIT:</strong> ${data.dni_nit}</p>
                    `;
                    const infoDialog = new bootstrap.Modal(document.getElementById('infoDialog'), { backdrop: 'static', keyboard: true });
                    infoDialog.show();
                })
                .catch(error => console.error('Error:', error));
        }

        /* Función editar cliente */
        function showEdit(id) {
            fetch(`/customers/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editLastname').value = data.lastname;
                    document.getElementById('editDniNit').value = data.dni_nit;
                    document.getElementById('modalTitle').innerText = 'Editar Datos - Cliente';
                    const editDialog = new bootstrap.Modal(document.getElementById('editDialog'), { backdrop: 'static', keyboard: true });
                    editDialog.show();
                    document.getElementById('saveEditButton').onclick = function () {
                        const formData = new FormData(document.getElementById('editCustomerForm'));
                        fetch(`/customers/update/${id}`, {
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

        /* Función nuevo cliente */
        function showNew() {
            document.getElementById('editName').value = '';
            document.getElementById('editLastname').value = '';
            document.getElementById('editDniNit').value = '';
            document.getElementById('modalTitle').innerText = 'Nuevo Cliente';
            const editDialog = new bootstrap.Modal(document.getElementById('editDialog'), { backdrop: 'static', keyboard: true });
            editDialog.show();
            document.getElementById('saveEditButton').onclick = function () {
                const formData = new FormData(document.getElementById('editCustomerForm'));
                fetch(`/customers/store`, {
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
