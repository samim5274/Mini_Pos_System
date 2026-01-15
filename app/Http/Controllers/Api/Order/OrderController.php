<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;

class OrderController extends Controller
{
    public function confirmOrder(Request $request){

        $request->validate([
            'reg' => 'required',
        ]);

        $userId = Auth::id();
        $reg    = $request->reg;

        $data = Order::with('tenant','user')->where('reg', $reg)->first();
        if($data){
            return response()->json([
                'error' => 'This order already placed. Try another one. Thank You..!'
            ], 404);
        }
        
        // Get cart items by reg & user
        $cartItems = Cart::with('product')->where('reg', $reg)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'error' => 'No cart data found for this reg'
            ], 404);
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->quantity * $item->product->price;
        }

        // // Create Order
        $order = Order::create([
            'reg'       => $reg,
            'date'      => now()->toDateString(),
            'user_id'   => $userId,
            'tenant_id' => $cartItems->first()->tenant_id,
            'status'    => 'Pending',
            'total'     => $total
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order'   => $order
        ], 201);
    }

    public function cancelOrder(Request $request){
        $request->validate([
            'reg' => 'required',
        ]);

        $reg    = $request->reg;

        // Get cart items by reg & user
        $cartItems = Cart::with('product')->where('reg', $reg)->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'error' => 'No cart data found for this reg'
            ], 404);
        }

        

        foreach ($cartItems as $item) {
            $product = Product::where('id', $item->product_id)->first();
            if ($product) {
                $product->stock_quantity += $item->quantity;
                $product->update();
            }
        }

        // Create Order
        $order = Order::where('reg', $reg)->where('user_id', Auth::id())->first();
        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        $order->status = 'Cancelled';
        $order->update();

        return response()->json([
            'message' => 'Order canceled successfully',
            'order'   => $order
        ], 200);
    }
}
