<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Carrito de Ventas') }}
            </h2>
            <a href="{{ route('cart.details') }}" class="btn btn-success d-flex align-items-center"><i class="fa fa-list me-2"></i> Detalles</a>
        </div>
    </x-slot>
    <!-- Contenido -->
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 p-10">
            <!-- Contenedor -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container py-3">
                    <!-- Barra de Busqueda -->
                    <form id="searchForm" class="d-flex w-100 mb-4">
                        <input type="text" id="searchInput" name="search" class="form-control mr-2" placeholder="Buscar producto" aria-label="Buscar producto">
                        &nbsp;
                        <button type="button" class="btn btn-secondary" onclick="searchProducts()"><i class="fas fa-search"></i></button>
                    </form>
                    <!-- Tabla de productos -->
                    <table class="table mt-3" id="products-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchProducts();
                } else if (event.key === 'Escape') {
                    event.preventDefault();
                    this.value = '';
                }
            });
        });

        function searchProducts() {
            const query = document.getElementById('searchInput').value;
            fetch('{{ route("products.search") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ search: query })
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#products-table tbody');
                tbody.innerHTML = '';

                data.products.forEach(product => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${product.name}</td>
                        <td>${product.stock}</td>
                        <td>${product.price} Bs</td>
                        <td>
                            <input type="number" class="form-control quantity" value="1" min="1">
                        </td>
                        <td>
                            <button class="btn btn-primary" onclick="addCart(${product.id}, this)">Añadir al Carrito</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
        }

        function addCart(productId, button) {
            const quantityInput = button.closest('tr').querySelector('.quantity');
            const quantity = quantityInput.value;

            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      document.querySelector("#cart-mount").innerHTML = data.mount + " Bs";
                  }
              });
        }
    </script>
</x-app-layout>
