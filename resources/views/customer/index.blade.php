<x-app-layout>
    
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Clientes') }}
            </h2>
            <button class="btn btn-success d-flex align-items-center" onclick="showNew()">
                <i class="fa fa-plus me-2"></i> Nuevo Registro
            </button>
        </div>
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
