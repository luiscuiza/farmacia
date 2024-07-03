<x-app-layout>
    <!-- Header -->
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
                <i class="fa fa-plus me-2"></i> Nuevo Laboratorio
            </button>
        </x-slot>





    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Tabla -->
            <table class="table mt-3" id="laboratories-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laboratories as $laboratory)
                        <tr id="lab-{{ $laboratory->id }}">
                            <td>{{ $laboratory->name }}</td>
                            <td>{{ $laboratory->phone }}</td>
                            <td>
                                <a class="btn btn-primary" onclick="showInfo({{ $laboratory->id }})">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning" onclick="showEdit({{ $laboratory->id }})">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-danger" onclick="confirmDelete({{ $laboratory->id }})">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $laboratories->links() }}
        </div>
    </div>

    @include("laboratory.dialogs")

</x-app-layout>
