<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request) {
        $products = Product::where('active', true)->when($request->q, fn($q, $term) => $q->where(fn($x) => $x->where('name', 'like', "%$term%")->orWhere('sku', 'like', "%$term%")))->orderBy('name')->paginate(24)->withQueryString();
        return view('store.index', compact('products'));
    }

    public function checkout(Request $request) {
        $cart = session('cart', []); abort_if(!$cart, 422, 'El carrito está vacío.');
        $ids = array_keys($cart); $products = Product::whereIn('id', $ids)->get()->keyBy('id'); $total = 0;
        foreach ($cart as $id => $qty) { if (isset($products[$id])) $total += $products[$id]->price * $qty; }
        $order = Order::create($request->validate(['customer_name'=>'required|string|max:120','email'=>'nullable|email','phone'=>'nullable|string|max:40','address'=>'nullable|string|max:200','notes'=>'nullable|string|max:500']) + ['total'=>$total]);
        foreach ($cart as $id => $qty) if (isset($products[$id])) $order->items()->create(['product_id'=>$id,'product_name'=>$products[$id]->name,'quantity'=>$qty,'unit_price'=>$products[$id]->price,'subtotal'=>$products[$id]->price*$qty]);
        session()->forget('cart'); return redirect()->route('store.index')->with('success', "Pedido #{$order->id} recibido. Te contactaremos para coordinarlo.");
    }
}
