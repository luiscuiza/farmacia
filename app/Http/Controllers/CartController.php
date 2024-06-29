<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Customer;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return view('cart.index');
    }

    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado.'], 404);
        }
        $cart = session()->get('cart', []);
        $total = session()->get('total', 0);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price
            ];
        }
        $total += $product->price * $quantity;
        session()->put('cart', $cart);
        session()->put('total', $total);
        return response()->json(['success' => true, 'mount' => $total]);
    }

    public function details() {
        $cart = session()->get('cart', []);
        return view('cart.detail', compact('cart'));
    }

    public function update(Request $request)
    {
        $productId = $request->input('id');
        $quantity = $request->input('quantity');
        if (!$productId || !$quantity || $quantity < 1) {
            return response()->json(['success' => false, 'message' => 'Cantidad no válida.'], 400);
        }
        $product = Product::with(['batches' => function($query) {
            $query->where('expiration', '>', now());
        }])->find($productId);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado.'], 404);
        }
        $availableStock = $product->batches->sum('quantity');
        if ($quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'maxstock'=> $availableStock,
                'message' => "No hay suficiente stock.\nStock disponible: $availableStock del producto: {$product->name}"
            ]);
        }
        $cart = session()->get('cart', []);
        $total = session()->get('total', 0);
        if (isset($cart[$productId])) {
            $oldQuantity = $cart[$productId]["quantity"];
            $cart[$productId]["quantity"] = $quantity;
            session()->put('cart', $cart);
            $total -= $cart[$productId]['price'] * $oldQuantity;
            $total += $cart[$productId]['price'] * $quantity;
            session()->put('total', $total);
            return response()->json(['success' => true, 'message' => 'Cantidad actualizada correctamente.']);
        }
        return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito.'], 404);
    }

    public function clear()
    {
        session()->put('total', 0);
        session()->forget('cart');
        session()->flash('success', 'Carrito vaciado.');
        return redirect()->route('cart.index');
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart', []);
            $total = session()->get('total', 0);
            if(isset($cart[$request->id])) {
                $total -= $cart[$request->id]['price'] * $cart[$request->id]['quantity'];
                unset($cart[$request->id]);
                session()->put('cart', $cart);
                session()->put('total', $total);
            }
            session()->flash('success', 'Producto eliminado correctamente');
            return response()->json(['success' => true, 'message' => 'Producto eliminado correctamente']);
        }
        return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
    }

    public function sell(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', '¡El carrito está vacío!');
        }
    
        // Validar los datos del cliente
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'dni_nit' => 'required|string|max:255',
        ]);
    
        // Crear o buscar al cliente
        $customer = Customer::firstOrCreate(
            ['dni_nit' => $request->dni_nit],
            ['name' => $request->name, 'lastname' => $request->lastname]
        );
    
        // Verificar el stock disponible antes de realizar la venta
        $insufficientStock = [];
        foreach ($cart as $id => $details) {
            $product = Product::with(['batches' => function($query) {
                $query->where('expiration', '>', now())->orderBy('expiration');
            }])->find($id);
    
            if (!$product) {
                return redirect()->route('cart.index')->with('error', "Producto con ID $id no encontrado.");
            }
    
            $availableStock = $product->batches->sum('quantity');
            if ($details['quantity'] > $availableStock) {
                $insufficientStock[$product->name] = $availableStock;
            }
        }
    
        if (!empty($insufficientStock)) {
            $errorMessages = [];
            foreach ($insufficientStock as $productName => $availableStock) {
                $errorMessages[] = "No hay suficiente stock de $productName. Stock disponible: $availableStock.";
            }
            return redirect()->route('cart.index')->with('error', implode("\n", $errorMessages));
        }
    
        // Crear la venta
        $sale = Sale::create([
            'date' => now(),
            'total' => 0, // Inicializar con 0, se actualizará después
            'user_id' => auth()->id(),
            'customer_id' => $customer->id,
        ]);
    
        // Crear detalles de venta y actualizar el stock
        $saleTotal = 0;
        foreach ($cart as $id => $details) {
            $product = Product::with(['batches' => function($query) {
                $query->where('expiration', '>', now())->orderBy('expiration');
            }])->find($id);
    
            $quantityToDeduct = $details['quantity'];
    
            foreach ($product->batches as $batch) {
                if ($quantityToDeduct <= 0) {
                    break;
                }
    
                if ($batch->quantity >= $quantityToDeduct) {
                    $batch->quantity -= $quantityToDeduct;
                    $batch->save();
                    $quantityToDeduct = 0;
                } else {
                    $quantityToDeduct -= $batch->quantity;
                    $batch->quantity = 0;
                    $batch->save();
                }
            }
    
            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $id,
                'quantity' => $details['quantity'],
                'price' => $details['price'],
            ]);
    
            $saleTotal += $details['price'] * $details['quantity'];
        }
    
        // Actualizar el total de la venta
        $sale->total = $saleTotal;
        $sale->save();
    
        // Limpiar el carrito
        session()->put('total', 0);
        session()->forget('cart');
    
        return redirect()->route('cart.index')->with('success', '¡Venta realizada correctamente!');
    }
    
}
