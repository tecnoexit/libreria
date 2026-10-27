<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard() { return view('admin.dashboard', ['products'=>Product::latest()->paginate(18), 'orders'=>Order::latest()->take(6)->get(), 'factor'=>Setting::where('key','monedas')->value('value') ?? '1.00']); }
    public function saveFactor(Request $request) { $data=$request->validate(['monedas'=>'required|numeric|min:0.01']); Setting::updateOrCreate(['key'=>'monedas'], ['value'=>$data['monedas']]); Product::query()->update(['price'=>\DB::raw('cost * '.$data['monedas'])]); return back()->with('success','Factor actualizado y precios recalculados.'); }
    public function storeProduct(Request $request) { $data=$request->validate(['name'=>'required|max:180','sku'=>'nullable|max:60','category'=>'nullable|max:100','cost'=>'required|numeric|min:0','stock'=>'required|integer|min:0','image'=>'nullable|image|max:2048']); $data['price']=$data['cost']*(float)(Setting::where('key','monedas')->value('value') ?? 1); if($request->hasFile('image')) $data['image']=$request->file('image')->store('products','public'); Product::create($data); return back()->with('success','Producto creado.'); }
    public function updateProduct(Request $request, Product $product) { $data=$request->validate(['name'=>'required|max:180','sku'=>'nullable|max:60','category'=>'nullable|max:100','cost'=>'required|numeric|min:0','stock'=>'required|integer|min:0','active'=>'nullable|boolean','image'=>'nullable|image|max:2048']); $data['price']=$data['cost']*(float)(Setting::where('key','monedas')->value('value') ?? 1); if($request->hasFile('image')) $data['image']=$request->file('image')->store('products','public'); $product->update($data); return back()->with('success','Producto actualizado.'); }
}
