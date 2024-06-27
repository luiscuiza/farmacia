<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Ventas') }}
            </h2>
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
                    <table class="table mt-3" id="sales-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Usuario</th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr id="sale-{{ $sale->id }}">
                                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}</td>
                                    <td>Bs. {{ $sale->total }}</td>
                                    <td>{{ $sale->user->name }}</td>
                                    <td>{{ $sale->customer->name }} {{ $sale->customer->lastname }}</td>
                                    <td>
                                        <button class="btn btn-info" onclick="showInfo({{ $sale->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="confirmDelete({{ $sale->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Eliminar Venta -->
    <div class="modal fade" id="confirmDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar esta venta?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="deleteButton">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Mostrar Venta -->
    <div class="modal fade" id="infoDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Información de la Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="infoDialogContent">Cargando...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="printInfo()">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Editar/Crear Venta -->
    <div class="modal fade" id="editDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Editar Datos - Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSaleForm">
                        <div class="mb-3">
                            <label for="editDate" class="form-label">Fecha</label>
                            <input type="datetime-local" class="form-control" id="editDate" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTotal" class="form-label">Total</label>
                            <input type="number" step="0.01" class="form-control" id="editTotal" name="total" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUser" class="form-label">Usuario</label>
                            <select class="form-control" id="editUser" name="user_id" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editCustomer" class="form-label">Cliente</label>
                            <select class="form-control" id="editCustomer" name="customer_id" required>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editDetails" class="form-label">Detalles</label>
                            <div id="editDetails">
                                <!-- Aquí se agregarán los detalles dinámicamente -->
                            </div>
                            <button type="button" class="btn btn-primary mt-2" onclick="addDetail()">Añadir Producto</button>
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
                document.getElementById('editSaleForm').reset();
                document.getElementById('saveEditButton').onclick = null;
                document.getElementById('editDetails').innerHTML = '';
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

        /* Función eliminar venta */
        function confirmDelete(id) {
            const confirmDialog = new bootstrap.Modal(document.getElementById('confirmDialog'), { backdrop: 'static', keyboard: true });
            confirmDialog.show();
            document.getElementById('deleteButton').onclick = function () {
                confirmDialog.hide();
                fetch(`/sales/destroy/${id}`, {
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
                    showMessage(false, 'Error al eliminar la venta.', false, 1000);
                });
            };
        }

        /* Función mostrar venta */
        function showInfo(id) {
            fetch(`/sales/${id}`)
                .then(response => response.json())
                .then(data => {
                    let itemsHtml = '';
                    data.sale_details.forEach((detail, index) => {
                        let price = parseFloat(detail.price);
                        itemsHtml += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${detail.product.name}</td>
                                <td>${detail.quantity}</td>
                                <td>${price.toFixed(2)}</td>
                                <td>${(detail.quantity * price).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    document.getElementById('infoDialogContent').innerHTML = `
                        <table class="table" >
                            <tr>
                                <th colspan="1">Cliente</th>
                                <td colspan="5">${data.customer.name} ${data.customer.lastname}</td>
                            </tr>
                            <tr>
                                <th colspan="1">Fecha</th>
                                <td colspan="2">${new Date(data.date).toLocaleDateString()}</td>
                                <th colspan="1">DNI/NIT</th>
                                <td colspan="2">${data.customer.dni_nit}</td>
                            </tr>
                        </table>
                        <br>
                        <table class="table" >
                            <tr>
                                <th>Item</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>P.U.</th>
                                <th>Subtotal</th>
                            </tr>
                            ${itemsHtml}
                            <tr>
                                <td colspan="4" class="text-end"><strong>TOTAL Bs.</strong></td>
                                <td>Bs. ${parseFloat(data.total).toFixed(2)}</td>
                            </tr>
                        </table>
                    `;
                    const infoDialog = new bootstrap.Modal(document.getElementById('infoDialog'), { backdrop: 'static', keyboard: true });
                    infoDialog.show();
                })
                .catch(error => console.error('Error:', error));
        }
        
        /* Imprimir */
        function printInfo() {
            var content = document.getElementById('infoDialogContent').innerHTML;
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Información de la Venta</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
            printWindow.document.write('table, th, td { border: 1px solid black; padding: 8px; text-align: left; }');
            printWindow.document.write('th { background-color: #f2f2f2; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</x-app-layout>
