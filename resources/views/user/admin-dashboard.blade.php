<div class="grid md:grid-cols-2 lg:grid-cols-2 gap-3 px-3 py-3">
    <div class="p-4 rounded-lg alert alert-primary">
        <h4 class="text-lg font-semibold">Ventas Diarias</h4>
        <p>Total vendido hoy: Bs. {{ number_format($totalSalesToday, 2) }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-primary">
        <h4 class="text-lg font-semibold">Ventas Mensuales</h4>
        <p>Total de ventas este mes: Bs. {{ number_format($totalSalesMonth, 2) }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-warning">
        <h4 class="text-lg font-semibold">Stock Bajo</h4>
        <p>Productos con bajo: {{ $lowStockProducts }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-warning">
        <h4 class="text-lg font-semibold">Stock Vacio</h4>
        <p>Producto sin stock: {{ $outStockProducts }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-danger">
        <h4 class="text-lg font-semibold">Lotes Expirados</h4>
        <p>Expirado este mes: {{ $expiredsMonth }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-danger">
        <h4 class="text-lg font-semibold">Lotes Próximos a Expirar</h4>
        <p>Expiran dentro de 7 dias: {{ $expiringNextWeek }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-dark">
        <h4 class="text-lg font-semibold">Lotes Vencidos</h4>
        <p>Lotes: {{ $expiredBatches }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-dark">
        <h4 class="text-lg font-semibold">Productos Vencidos</h4>
        <p>Lotes: {{ $expiredProducts }}</p>
    </div>
</div>
