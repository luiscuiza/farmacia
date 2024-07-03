<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center bg-white">
            <h2 class="h5 text-dark font-weight-bold">
                {{ __('Carrito de Ventas') }}
            </h2>
            <button class="btn btn-danger d-flex align-items-center" onclick="clearCart()">
                <i class="fa fa-broom me-2"></i> Vaciar Carrito
            </button>
        </div>
    </x-slot>

    <!-- Contenido -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="container py-3">
            <!-- Tabla -->
            <table class="table mt-3" id="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cart as $id => $details)
                        <tr id="cart-item-{{ $id }}" data-price="{{ $details['price'] }}">
                            <td>{{ $details['name'] }}</td>
                            <td>{{ $details['price'] }}</td>
                            <td colspan="1">
                                <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control" style="width: 100px;" min="1" onfocus="storePreviousValue(this)" oninput="updateSubtotal(this)" onblur="checkAndUpdateCart({{ $id }}, this)">
                            </td>
                            <td class="subtotal">{{ $details['price'] * $details['quantity'] }}</td>
                            <td style="width: 40px;">
                                <button class="btn btn-danger" onclick="confirmDeleteProduct({{ $id }})"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">El carrito está vacío</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total Bs:</strong></td>
                        <td colspan="2" class="text-start"><strong id="cart-total">{{ session('total', 0) }}</strong></td>
                    </tr>
                </tbody>
            </table>
            @if(count($cart) > 0)    
                <button class="btn btn-success text-end" onclick="confirmSell()"><i class="fa fa-dollar-sign me-2"></i> Realizar Venta</button>
            @endif
        </div>
    </div>

    @include("cart.dialogs")

</x-app-layout>
