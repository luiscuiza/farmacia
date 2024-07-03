<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Proveedores') }}
            </h2>
            <button class="btn btn-success d-flex align-items-center" onclick="showNew()">
                <i class="fa fa-plus me-2"></i> Nuevo Proveedor
            </button>
        </div>
    </x-slot>

    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Tabla -->
            <table class="table mt-3" id="suppliers-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr id="supplier-{{ $supplier->id }}">
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>
                                <a class="btn btn-primary" onclick="showInfo({{ $supplier->id }})">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning" onclick="showEdit({{ $supplier->id }})">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-danger" onclick="confirmDelete({{ $supplier->id }})">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $suppliers->links() }}
        </div>
    </div>

    @include("supplier.dialogs")

</x-app-layout>
