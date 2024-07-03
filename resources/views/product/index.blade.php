@php
    $user = Auth::user();
@endphp


<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.index')">
            {{ __('Productos') }}
        </x-nav-link>
        @if ($user->role == 'admin')
            <x-nav-link href="{{ route('batches.index') }}" :active="request()->routeIs('batches.index')">
                {{ __('Lotes') }}
            </x-nav-link>
        @endif
    </x-slot>
    @if ($user->role == 'admin')
        <x-slot name="options">
            <button class="btn btn-success d-flex align-items-center m-2.5" onclick="showNew()">
                <i class="fa fa-plus me-2"></i> Nuevo Producto
            </button>  
        </x-slot>
    @endif
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
                                @if ($user->role == 'admin')
                                    <a class="btn btn-warning" onclick="showEdit({{ $product->id }})">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn btn-danger" onclick="confirmDelete({{ $product->id }})">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                @endif
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
