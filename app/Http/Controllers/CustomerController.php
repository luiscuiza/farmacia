<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(10);
        return view('customer.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'dni_nit' => 'required|string|unique:customers,dni_nit',
        ]);

        Customer::create($request->all());

        return response()->json(['success' => true, 'message' => 'Cliente creado correctamente']);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'dni_nit' => 'required|string|unique:customers,dni_nit,' . $id,
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return response()->json(['success' => true, 'message' => 'Cliente actualizado correctamente']);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['success' => true, 'message' => 'Cliente eliminado correctamente']);
    }
}
