<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Proveedores') }}
            </h2>
            <button class="btn btn-success d-flex align-items-center" onclick="showCreate()">
                <i class="fa fa-plus me-2"></i> Nuevo Proveedor
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
                    <table class="table mt-3" id="suppliers-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $supplier)
                                <tr id="supplier-{{ $supplier->id }}">
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>
                                        <a class="btn btn-info" onclick="showInfo({{ $supplier->id }})">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a class="btn btn-warning" onclick="showEdit({{ $supplier->id }})">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a class="btn btn-danger" onclick="confirmDelete({{ $supplier->id }})">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Eliminar Proveedor -->
    <div class="modal fade" id="confirmDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este proveedor?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="deleteButton">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Mostrar Proveedor -->
    <div class="modal fade" id="infoDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Información del Proveedor</h5>
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
    <!-- Modal Editar/Crear Proveedor -->
    <div class="modal fade" id="editDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Editar Datos - Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSupplierForm">
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
        document.addEventListener('DOMContentLoaded', function () {
            
            document.getElementById('infoDialog').addEventListener('hidden.bs.modal', function () {
                document.getElementById('infoDialogContent').innerHTML = "";
            });
            
            document.getElementById('editDialog').addEventListener('hidden.bs.modal', function () {
                document.getElementById('editSupplierForm').reset();
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

        /* Función eliminar proveedor */
        function confirmDelete(id) {
            const confirmDialog = new bootstrap.Modal(document.getElementById('confirmDialog'), { backdrop: 'static', keyboard: true });
            confirmDialog.show();
            document.getElementById('deleteButton').onclick = function () {
                confirmDialog.hide();
                fetch(`/suppliers/destroy/${id}`, {
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
                    showMessage(false, 'Error al eliminar el proveedor.', false, 1000);
                });
            };
        }

        /* Función mostrar proveedor */
        function showInfo(id) {
            fetch(`/suppliers/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('infoDialogContent').innerHTML = `
                        <p><strong>Nombre:</strong> ${data.name}</p>
                        <p><strong>Teléfono:</strong> ${data.phone}</p>
                        <p><strong>Email:</strong> ${data.email}</p>
                        <p><strong>Laboratorio:</strong> ${data.laboratory ? data.laboratory.name : 'N/A'}</p>
                    `;
                    const infoDialog = new bootstrap.Modal(document.getElementById('infoDialog'), { backdrop: 'static', keyboard: true });
                    infoDialog.show();
                })
                .catch(error => console.error('Error:', error));
        }

        /* Función editar proveedor */
        function showEdit(id) {
            fetch(`/suppliers/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editPhone').value = data.phone;
                    const labSelect = document.getElementById('editLaboratory');
                    labSelect.value = data.laboratory ? data.laboratory.id : '';
                    document.getElementById('modalTitle').innerText = 'Editar Datos - Proveedor';
                    const editDialog = new bootstrap.Modal(document.getElementById('editDialog'), { backdrop: 'static', keyboard: true });
                    editDialog.show();
                    document.getElementById('saveEditButton').onclick = function () {
                        const formData = new FormData(document.getElementById('editSupplierForm'));
                        fetch(`/suppliers/update/${id}`, {
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

        /* Función nuevo proveedor */
        function showCreate() {
            document.getElementById('editName').value = '';
            document.getElementById('editEmail').value = '';
            document.getElementById('editPhone').value = '';
            document.getElementById('modalTitle').innerText = 'Nuevo Proveedor';
            const editDialog = new bootstrap.Modal(document.getElementById('editDialog'), { backdrop: 'static', keyboard: true });
            editDialog.show();
            document.getElementById('saveEditButton').onclick = function () {
                const formData = new FormData(document.getElementById('editSupplierForm'));
                fetch(`/suppliers/store`, {
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
