<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Supplier;
use App\Models\Laboratory;

class SupplierController extends Controller
{

    public function index()
    {
        $suppliers = Supplier::paginate(10);
        $laboratories = Laboratory::all();
        return view('supplier.index', compact('suppliers', 'laboratories'));
    }

    public function info($id)
    {
        $supplier = Supplier::with('laboratory')->findOrFail($id);
        return response()->json($supplier);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:suppliers',
            'phone' => 'nullable|string|max:20',
            'laboratory_id' => 'nullable|exists:laboratories,id',
        ]);
        $supplier = Supplier::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'laboratory_id' => $request->laboratory_id,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Proveedor creado correctamente',
            'data' => $supplier
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:suppliers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'laboratory_id' => 'nullable|exists:laboratories,id',
        ]);
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'laboratory_id' => $request->laboratory_id,
            ]);
            return response()->json(['success' => true, 'message' => 'Proveedor actualizado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el proveedor' ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
            return response()->json(['success' => true, 'message' => 'Proveedor eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el proveedor.'], 500);
        }
    }
}
