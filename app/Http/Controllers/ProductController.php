<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::with('order')
                ->get()
                ->map(function ($product) {
                    $totalSold = $product->order->sum('pivot.quantity');

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'sold' => $totalSold,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at
                    ];
                });

            return response()->json([
                'message' => 'Product List',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve products: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|numeric|min:0',
        ], [
            'name.string' => 'The name must be a string!',
            'name.unique' => 'The product name already exists. Please choose a different name.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 1 or equal to 1!',
            'stock.numeric' => 'The stock must be a number.',
            'stock.min' => 'The stock cannot be negative!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = new Product();
            $data->name = $request->input('name');
            $data->price = $request->input('price');
            $data->stock = $request->input('stock');

            $data->save();

            $responseData = [
                'id' => $data->id,
                'name' => $data->name,
                'price' => $data->price,
                'stock' => $data->stock,
                'sold' => 0,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at
            ];

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $responseData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Product failed created! ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with('order')->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            $totalSold = $product->order->sum('pivot.quantity');

            $responseData = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'sold' => $totalSold,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];

            return response()->json([
                'message' => 'Product Detail',
                'data' => $responseData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve product: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|unique:products,name,' . $id,
            'price' => 'nullable|numeric|min:1',
            'stock' => 'nullable|numeric|min:0',
        ], [
            'name.string' => 'The name must be a string!',
            'name.unique' => 'The product name already exists. Please choose a different name.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 1 or equal to 1!',
            'stock.numeric' => 'The stock must be a number.',
            'stock.min' => 'The stock cannot be negative!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::with('order')->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            $product->name = $request->name ?? $product->name;
            $product->price = $request->price ?? $product->price;
            $product->stock = $request->stock ?? $product->stock;

            $product->save();

            $totalSold = $product->order->sum('pivot.quantity');

            $responseData = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'sold' => $totalSold,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $responseData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Product failed updated! ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $product = Product::with('order')->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            foreach ($product->order as $order) {
                $order->product()->detach($product->id);

                if ($order->product()->count() == 0) {
                    $order->delete();
                }
            }

            $totalSold = $product->order->sum('pivot.quantity');

            $responseData = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'sold' => $totalSold,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];

            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully',
                'data' => $responseData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
