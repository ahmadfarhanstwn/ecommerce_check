<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        return response()->json(Product::all(), 200);
    }

    public function store(Request $request) {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'units' => $request->unit,
            'price' => $request->price,
            'image' => $request->image,
        ]);

        return response()->json([
            'status' => (bool)$product,
            'data' => $product,
            'message' => $product ? 'Product created!' : 'Failed to create product'
        ]);
    }

    public function show(Product $product) {
        return response()->json($product, 200);
    }

    public function update(Request $request, Product $product) {
        $status = $product->update(
            $request->only(['name', 'description', 'category', 'units', 'price', 'image'])
        );

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product updated!' : 'Failed to update product'
        ]);
    }

    public function updateUnits(Request $request, Product $product) {
        $product->units = $product->units + $request->get('units');
        $status = $product->save();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Units added!' : 'Failed to add unit'
        ]);
    }

    public function destroy(Product $product) {
        $status = $product->delete();

        return response([
            'status' => $status,
            'message' => $status ? 'Product deleted!' : 'Failed to delete product'
        ]);
    }

    public function uploadImage(Request $request) {
        if($request->hasFile('image')){
            $name = time()."_".$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $name);
        }
        return response()->json(asset("images/$name"),201);
    }
}
