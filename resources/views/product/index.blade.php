<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Productos') }}
            </h2>
            <button class="btn btn-success d-flex align-items-center" onclick="showNew()">
                <i class="fa fa-plus me-2"></i> Nuevo Producto
            </button>
        </div>
    </x-slot>

    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Tabla -->
            <table class="table mt-3" id="products-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr id="product-{{ $product->id }}">
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->price }}</td>
                            <td>
                                <a class="btn btn-primary" onclick="showInfo({{ $product->id }})">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning" onclick="showEdit({{ $product->id }})">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-danger" onclick="confirmDelete({{ $product->id }})">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
    
    @include('product.dialogs')

</x-app-layout>
