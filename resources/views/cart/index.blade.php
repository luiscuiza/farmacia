<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <x-nav-link href="{{ route('cart.index') }}" :active="request()->routeIs('cart.index')">
            {{ __('Buscar Producto') }}
        </x-nav-link>
        <x-nav-link href="{{ route('cart.details') }}" :active="request()->routeIs('cart.details')">
            {{ __('Lista de Productos') }}
        </x-nav-link>
    </x-slot>

    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Barra de Busqueda -->
            <form id="searchForm" class="d-flex w-100 mb-4">
                <input type="text" id="searchInput" name="search" class="form-control mr-2" placeholder="Buscar producto" aria-label="Buscar producto">
                &nbsp;
                <button type="button" class="btn btn-secondary" onclick="searchProducts()"><i class="fas fa-search"></i></button>
                &nbsp;
                <a href="{{ route('cart.details') }}" class="btn btn-success d-flex align-items-center"><i class="fa fa-list"></i></a>
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
    
    <script>
        /* KeyBinds Enter, Escape for SearchBar */
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchProducts();
                } else if (event.key === 'Escape') {
                    event.preventDefault();
                    this.value = '';
                    document.querySelector('#products-table tbody').innerHTML = '';
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

                if (data.products.length === 0) {
                    $.notification({
                        title: 'Búsqueda',
                        message: 'No se encontraron resultados.',
                        type: 'info',
                        delay: 3000
                    });
                    return;
                }

                const fragment = document.createDocumentFragment();

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
                            <button class="btn btn-primary" onclick="addCart(${product.id}, this)"><i class="fa fa-shopping-cart"></i></button>
                        </td>
                    `;
                    fragment.appendChild(row);
                });

                tbody.appendChild(fragment);
            })
            .catch(error => {
                console.error('Error:', error);
                $.notification({
                    title: 'Error',
                    message: 'Ocurrió un error al buscar los productos.',
                    type: 'danger',
                    delay: 3000
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
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector("#cart-mount").innerHTML = data.mount + " Bs";
                    $.notification({
                        title: 'Éxito',
                        message: 'Producto añadido al carrito.',
                        type: 'success',
                        delay: 2000
                    });
                } else {
                    $.notification({
                        title: 'Error',
                        message: 'No se pudo añadir el producto al carrito.',
                        type: 'danger',
                        delay: 2500
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $.notification({
                    title: 'Error',
                    message: 'Ocurrió un error al intentar añadir el producto al carrito.',
                    type: 'danger',
                    delay: 3000
                });
            });
        }
    </script>

</x-app-layout>
