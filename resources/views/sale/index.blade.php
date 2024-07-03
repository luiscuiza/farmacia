@php
    $user = Auth::user();
@endphp


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
                                <button class="btn btn-primary" onclick="showInfo({{ $sale->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if ($user->role == 'admin')
                                    <button class="btn btn-danger" onclick="confirmDelete({{ $sale->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $sales->links() }}
        </div>
    </div>

    @include("sale.dialogs")

</x-app-layout>
