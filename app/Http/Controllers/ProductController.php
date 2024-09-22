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
            $data = Product::all();

            return response()->json([
                'message' => 'Product List',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve product: ' . $e->getMessage(),
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

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $data
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
            $data = Product::find($id);

            if (!$data) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Product Detail',
                'data' => $data
            ]);
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
            $data = Product::find($id);

            if (!$data) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            $data->name = $request->name ?? $data->name;
            $data->price = $request->price ?? $data->price;
            $data->stock = $request->stock ?? $data->stock;

            $data->save();

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Product failed updated! ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            $data = Product::find($id);

            if (!$data) {
                return response()->json([
                    'message' => 'Product not found',
                ], 404);
            }

            $data->delete();

            return response()->json([
                'message' => 'Product deleted successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve product: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
