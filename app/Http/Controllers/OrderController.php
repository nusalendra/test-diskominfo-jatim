<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::with('product')->get();

            $data = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'products' => $order->product->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $product->pivot->quantity,
                            'stock' => $product->stock,
                            'sold' => $product->pivot->quantity,
                            'created_at' => $product->created_at,
                            'updated_at' => $product->updated_at,
                        ];
                    }),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ];
            });

            return response()->json([
                'message' => 'Order List',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products.*.id' => 'required|numeric',
            'products.*.quantity' => 'required|numeric|min:1',
        ], [
            'products.*.id.numeric' => 'The id must be a numeric!',
            'products.*.quantity.min' => 'The quantity must be at least 1!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                if (!$product) {
                    return response()->json([
                        'message' => 'Product not found',
                    ], 404);
                }

                if ($product->stock - $productData['quantity'] < 0) {
                    return response()->json([
                        'message' => 'Product out of stock',
                    ], 400);
                }
            }

            $order = new Order();
            $order->save();
            $products = [];

            foreach ($request->products as $productData) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $productData['id'];
                $orderProduct->quantity = $productData['quantity'];

                $orderProduct->save();

                $product = Product::find($productData['id']);
                $product->stock -= $orderProduct->quantity;
                $product->save();

                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $productData['quantity'],
                    'stock' => $product->stock,
                    'sold' => $productData['quantity'],
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            }

            return response()->json([
                'message' => 'Order created',
                'data' => [
                    'id' => $order->id,
                    'products' => $products,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order failed created! ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $order = Order::with('product')->find($id);
            
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }

            $data = [
                'id' => $order->id,
                'products' => $order->product->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $product->pivot->quantity,
                        'stock' => $product->stock,
                        'sold' => $product->pivot->quantity,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at,
                    ];
                }),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ];

            return response()->json([
                'message' => 'Order Detail',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve order: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            $order = Order::with('product')->find($id);
    
            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }
    
            foreach ($order->product as $product) {
                $product->stock += $product->pivot->quantity;
                $product->save();
            }
     
            $order->delete();
    
            return response()->json([
                'message' => 'Order deleted successfully',
                'data' => [
                    'id' => $order->id,
                    'products' => $order->product->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $product->pivot->quantity,
                            'stock' => $product->stock,
                            'sold' => 0,
                            'created_at' => $product->created_at,
                            'updated_at' => $product->updated_at,
                        ];
                    }),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete order: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
