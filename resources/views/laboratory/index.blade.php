<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Laboratorios') }}
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
