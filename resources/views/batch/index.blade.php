@php
    $user = Auth::user();
@endphp

<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        {{-- {{ __('Productos') }} --}}
        <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.index')">
            {{ __('Productos') }}
        </x-nav-link>
        @if ($user->role == 'admin')
            <x-nav-link href="{{ route('batches.index') }}" :active="request()->routeIs('batches.index')">
                {{ __('Lotes') }}
            </x-nav-link>
        @endif
    </x-slot>
    <x-slot name="options">
        <button class="btn btn-success d-flex align-items-center m-2.5" onclick="showNew()">
            <i class="fa fa-plus me-2"></i> Nuevo Lote
        </button>
    </x-slot>
    
    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Tabla -->
            <table class="table mt-3" id="batches-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Fecha de Expiración</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batches as $batch)
                        @php
                            $isExpired = \Carbon\Carbon::parse($batch->expiration)->isPast();
                            $isOutOfStock = $batch->stock == 0;
                        @endphp
                        <tr id="batch-{{ $batch->id }}" class="{{ $isOutOfStock ? 'table-secondary' : ($isExpired ? 'table-danger' : '') }}">
                            <td>{{ $batch->product ? $batch->product->name : '' }}</td>
                            <td>{{ $batch->stock }}</td>
                            <td>{{ $batch->expiration }}</td>
                            <td>
                                <a class="btn btn-primary" onclick="showInfo({{ $batch->id }})">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning" onclick="showEdit({{ $batch->id }})">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-danger" onclick="confirmDelete({{ $batch->id }})">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $batches->links() }}
        </div>
    </div>

    @include("batch.dialogs")
    
</x-app-layout>
