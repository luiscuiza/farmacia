<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;

class SaleController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'admin') {
            $sales = Sale::with('customer', 'user', 'saleDetails.product')->orderBy('date', 'desc')->paginate(10);
        } else {
            $sales = Sale::with('customer', 'user', 'saleDetails.product')->where('user_id', $user->id)->orderBy('date', 'desc')->paginate(10);
        }
        $users = User::all();
        $products = Product::all();
        $customers = Customer::all();
        return view('sale.index', compact('sales', 'users', 'customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'total' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'details' => 'required|array',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        $sale = Sale::create($request->only(['date', 'total', 'user_id', 'customer_id']));

        foreach ($request->details as $detail) {
            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Venta creada correctamente']);
    }

    public function info($id)
    {
        try {
            $sale = Sale::with(['customer', 'user', 'saleDetails.product'])->findOrFail($id);
            $sale->total = $sale->saleDetails->sum(function ($detail) {
                return $detail->price * $detail->quantity;
            });
            return response()->json($sale);
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Ocurrió un error al cargar la venta.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'total' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'details' => 'required|array',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->update($request->only(['date', 'total', 'user_id', 'customer_id']));

        $sale->saleDetails()->delete();
        foreach ($request->details as $detail) {
            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Venta actualizada correctamente']);
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->saleDetails()->delete();
        $sale->delete();

        return response()->json(['success' => true, 'message' => 'Venta eliminada correctamente']);
    }
}
