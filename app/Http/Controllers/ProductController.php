<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Laboratory;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        $laboratories = Laboratory::all();
        return view('product.index', compact('products', 'laboratories'));
    }

    public function info($id)
    {
        $product = Product::with('laboratory')->findOrFail($id);
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'barcode' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'laboratory_id' => 'nullable|exists:laboratories,id',
        ]);

        try {
            $product = Product::create($validatedData);
            return response()->json(['success' => true, 'message' => 'Producto creado exitosamente.', 'data' => $product]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el producto.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'barcode' => 'required|string|unique:products,barcode,' . $id,
            'description' => 'nullable|string',
            'laboratory_id' => 'nullable|exists:laboratories,id',
        ]);

        try {
            $product = Product::findOrFail($id);
            $product->update($validatedData);
            return response()->json(['success' => true, 'message' => 'Producto actualizado exitosamente.', 'data' => $product]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el producto.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el producto.'], 500);
        }
    }
    
    public function search(Request $request)
    {
        $query = $request->input('search');
        if (!$query) {
            return response()->json(['products' => []], 200);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->with(['batches' => function($query) {
                $query->where('expiration', '>', now());
            }])
            ->get();

        $products->each(function($product) {
            $product->stock = $product->batches->sum('quantity');
        });

        return response()->json(['products' => $products], 200);
    }
}