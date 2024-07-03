<x-app-layout>
    
    <!-- Header -->
    <x-slot name="header">
        <x-nav-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')">
            {{ __('Proveedores') }}
        </x-nav-link>
        <x-nav-link href="{{ route('laboratories.index') }}" :active="request()->routeIs('laboratories.index')">
            {{ __('Laboratorios') }}
        </x-nav-link>
        <x-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
            {{ __('Clientes') }}
        </x-nav-link>
    </x-slot>
    <x-slot name="options">
        <button class="btn btn-success d-flex align-items-center m-2.5" onclick="showNew()">
            <i class="fa fa-plus me-2"></i> Nuevo Cliente
        </button>
    </x-slot>
    
    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Tabla -->
            <table class="table mt-3" id="customers-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>DNI/NIT</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr id="customer-{{ $customer->id }}">
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->lastname }}</td>
                            <td>{{ $customer->dni_nit }}</td>
                            <td>
                                <button class="btn btn-primary" onclick="showInfo({{ $customer->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-warning" onclick="showEdit({{ $customer->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" onclick="confirmDelete({{ $customer->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $customers->links() }}
        </div>
    </div>
    
    @include("customer.dialogs")

</x-app-layout>
