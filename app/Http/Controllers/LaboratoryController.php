<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratory;

class LaboratoryController extends Controller
{
    public function index()
    {
        $laboratories = Laboratory::paginate(10);
        return view('laboratory.index', compact('laboratories'));
    }

    public function info($id)
    {
        $laboratory = Laboratory::findOrFail($id);
        return response()->json($laboratory);
    }

    public function destroy(Request $request)
    {
        /* return response()->json(['success' => false, 'message' => "Probando el mensaje js autoremoved"], 500); */
        try {
            $laboratory = Laboratory::findOrFail($request->id);
            $laboratory->delete();
            return response()->json(['success' => true, 'message' => 'Laboratorio eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el Laboratorio.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $laboratory = Laboratory::findOrFail($id);
            $laboratory->update($request->all());
            return response()->json(['success' => true, 'message' => 'Datos actualizado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error no se pudo guardar los cambios.'], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:laboratories',
        ]);
        try {
            $laboratory = Laboratory::create($validatedData);
            return response()->json(['success' => true, 'message' => 'Registro creado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el registro.'], 500);
        }
    }
    
}