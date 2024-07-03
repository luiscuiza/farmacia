@php
    $user = Auth::user();
@endphp

{{-- Dialog Info --}}
<script>
    function showInfo(id) {
        fetch(`/sales/${id}`)
        .then(response => {
            if (!response.ok) { throw new Error('Error: ' + response.status); }
            return response.json();
        })
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

            let dialogBody = `
                <table class="table">
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
                <table class="table">
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
                        <td>${parseFloat(data.total).toFixed(2)}</td>
                    </tr>
                </table>
            `;

            $.dialog({
                title: 'Información de la Venta',
                body: dialogBody,
                scrollable: true,
                buttons: { accept: true, cancel: true },
                buttonText: { accept: 'Imprimir', cancel: 'Cerrar' },
                onAccept: function() {
                    // Aquí puedes añadir la lógica para imprimir el contenido del diálogo
                    let printWindow = window.open('', '', 'height=600,width=800');
                    printWindow.document.write('<html><head><title>Imprimir Venta</title>');
                    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />');
                    printWindow.document.write('</head><body >');
                    printWindow.document.write(dialogBody);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.print();
                }
            });
        })
        .catch(error => {
            $.notification({
                title: 'Error', 
                message: 'No se pudo obtener los datos de la venta.', 
                type: 'danger', 
                delay: 3000
            });
            console.error('Error:', error);
        });
    }
</script>

@if ($user->role == 'admin')
{{-- Dialog Delete --}}
<script>
    function confirmDelete(id) {
        $.dialog({
            title: 'Eliminar Venta',
            body: '<p>¿Está seguro que desea eliminar esta venta?</p>',
            buttons: { accept: true, cancel: true },
            buttonText: { accept: 'Aceptar', cancel: 'Cancelar' },
            onAccept: function(dialogOperation) {
                fetch(`/sales/destroy/${id}`, {
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
                    $.notification({ title: 'Eliminar Venta', message: 'Eliminación exitosa.', type: 'success', delay: 0 });
                    setTimeout(() => { location.reload(); }, 1500);
                })
                .catch(error => { 
                    dialogOperation('close');
                    $.notification({ title: 'Eliminar Venta', message: 'Eliminación fallida.', type: 'danger', delay: 3000 });
                });
            }
        });
    }
</script>
@endif
