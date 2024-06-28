<div class="grid md:grid-cols-2 lg:grid-cols-2 gap-3 px-3 py-3">
    <div class="p-4 rounded-lg alert alert-primary">
        <h4 class="text-lg font-semibold">Ventas Diarias</h4>
        <p>Total vendido hoy: Bs. {{ number_format($totalSalesToday, 2) }}</p>
    </div>

    <div class="p-4 rounded-lg alert alert-primary">
        <h4 class="text-lg font-semibold">Ventas Mensuales</h4>
        <p>Total de ventas este mes: Bs. {{ number_format($totalSalesMonth, 2) }}</p>
    </div>
</div>
