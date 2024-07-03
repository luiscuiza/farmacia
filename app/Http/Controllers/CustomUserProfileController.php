<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Profile;

class CustomUserProfileController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        $profiles = Profile::all();
        return view('user.index', compact('users','profiles'));
    }

    public function info($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        Log::info($request);
        // Reglas de validación básicas
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            'profile_id' => 'nullable|exists:profiles,id',
        ];

        // Reglas de validación adicionales si no se selecciona un profile_id
        if (is_null($request->profile_id)) {
            $rules['profile_name'] = 'required|string|max:255';
            $rules['profile_lastname'] = 'required|string|max:255';
            $rules['profile_phone'] = 'required|string|max:255';
        }

        // Validar los datos del formulario
        $request->validate($rules);

        Log::info("validated");

        // Crear un nuevo perfil si no se seleccionó uno existente
        $profile_id = $request->profile_id;
        if (is_null($profile_id)) {
            $profile = Profile::create([
                'name' => $request->profile_name,
                'lastname' => $request->profile_lastname,
                'phone' => $request->profile_phone,
            ]);
            $profile_id = $profile->id;
        }

        // Crear el nuevo usuario
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_id' => $profile_id,
        ]);

        return response()->json(['message' => 'Creación exitosa.'], 201);
    }


    public function update(Request $request, $id)
    {
        Log::info($request);
    
        // Reglas de validación básicas
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string',
            'profile_id' => 'nullable|exists:profiles,id',
        ];
    
        // Reglas de validación adicionales si no se selecciona un profile_id
        if (is_null($request->profile_id)) {
            $rules['profile_name'] = 'required|string|max:255';
            $rules['profile_lastname'] = 'required|string|max:255';
            $rules['profile_phone'] = 'required|string|max:255';
        }
    
        // Validar los datos del formulario
        $request->validate($rules);
    
        // Encontrar el usuario a actualizar
        $user = User::findOrFail($id);
    
        // Actualizar el perfil si no se seleccionó uno existente
        $profile_id = $request->profile_id;
        if (is_null($profile_id)) {
            $profile = Profile::create([
                'name' => $request->profile_name,
                'lastname' => $request->profile_lastname,
                'phone' => $request->profile_phone,
            ]);
            $profile_id = $profile->id;
        }
    
        // Actualizar el usuario
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'profile_id' => $profile_id,
        ]);
    
        return response()->json(['message' => 'Actualización exitosa.']);
    }
    

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
    
            return response()->json(['message' => 'Eliminación exitosa.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Eliminación fallida.', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function resetpassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['message' => 'Contraseña actualizada exitosamente.']);
    }
}
