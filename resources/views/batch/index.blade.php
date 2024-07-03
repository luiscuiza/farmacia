<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Lotes') }}
            </h2>
            <button class="btn btn-success d-flex align-items-center" onclick="showNew()">
                <i class="fa fa-plus me-2"></i> Nuevo Lote
            </button>
        </div>
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
