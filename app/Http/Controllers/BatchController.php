<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Batch;
use App\Models\Product;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with('product')->paginate(10);
        $products = Product::all();
        return view('batch.index', compact('batches', 'products'));
    }

    public function info($id)
    {
        $batch = Batch::with('product')->findOrFail($id);
        return response()->json($batch);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:batches',
            'quantity' => 'required|integer',
            'stock' => 'required|integer',
            'expiration' => 'required|date',
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $batch = Batch::create($validatedData);
            return response()->json(['success' => true, 'message' => 'Lote creado exitosamente.', 'data' => $batch]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el lote.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:batches,code,' . $id,
            'quantity' => 'required|integer',
            'stock' => 'required|integer',
            'expiration' => 'required|date',
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $batch = Batch::findOrFail($id);
            $batch->update($validatedData);
            return response()->json(['success' => true, 'message' => 'Lote actualizado exitosamente.', 'data' => $batch]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el lote.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $batch = Batch::findOrFail($id);
            $batch->delete();
            return response()->json(['success' => true, 'message' => 'Lote eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el lote.'], 500);
        }
    }
}