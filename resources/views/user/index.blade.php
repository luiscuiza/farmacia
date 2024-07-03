<x-app-layout>
    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Usuarios') }}
            </h2>
        </div>
    </x-slot>
    <x-slot name="options">
        <button class="btn btn-success d-flex align-items-center m-2.5" onclick="showNew()">
            <i class="fa fa-plus me-2"></i> Nuevo Usuario
        </button>
    </x-slot>
    
    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Tabla -->
            <table class="table mt-3" id="batches-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr id="user-{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <a class="btn btn-primary" onclick="showInfo({{ $user->id }})">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning" onclick="showEdit({{ $user->id }})">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-warning" onclick="resetPassword({{ $user->id }})">
                                    <i class="fas fa-key"></i>
                                </a>
                                <a class="btn btn-danger" onclick="confirmDelete({{ $user->id }})">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>

    @include("user.dialogs")
    
</x-app-layout>
