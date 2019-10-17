<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ProductEventCreated;
use App\Events\ProductEventUpdated;
use App\Events\ProductEventDeleted;
use App\Product;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }
    
    public function store(Request $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->price = $request->price;
        $product->save();
        $product->status = 'created';

        event(new ProductEventCreated($product));

        return $product;
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->price = $request->price;
        $product->save();

        event(new ProductEventUpdated($product));

        return $product;
    }

    public function destroy($id)
    {
        $productDel = Product::find($id);
        $product = $productDel->id;
        $productDel->delete();

        event(new ProductEventDeleted($product));

        return $product;
    }
}
