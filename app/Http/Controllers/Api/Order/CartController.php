<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;

class CartController extends Controller
{
    public function addCart(Request $request, $id){

        // Logged-in user
        $user = Auth::user();

        // Validation
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Product fetch
        $product = Product::findOrFail($id);

        // Stock check
        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'error' => 'Not enough stock available'
            ], 400);
        }

        // Generate reg (same day + same user)
        $userId = $user->id;
        $count = Order::whereDate('date', now())
            ->where('user_id', $userId)
            ->count() + 1;

        $reg = now()->format('Ymd') . $userId . $count;

        // Check existing cart
        $cart = Cart::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->where('reg', $reg)
            ->first();

        if ($cart) {
            $newQty = $cart->quantity + $request->quantity;

            if ($newQty > $product->stock_quantity) {
                return response()->json([
                    'error' => 'Stock limit exceeded'
                ], 400);
            }

            
            $cart->update([
                'quantity'  => $newQty,
                'price'     => $product->price * $newQty,
            ]);

            $product->stock_quantity -= $request->quantity;
            $product->update();

            return response()->json([
                'message' => 'Cart quantity updated successfully',
                'data' => $cart
            ], 200);
        }

        // Create cart
        $cart = Cart::create([
            'reg'        => $reg,
            'product_id' => $product->id,
            'tenant_id'  => $product->tenant_id,
            'user_id'    => $userId,
            'price'      => $product->price,
            'quantity'   => $request->quantity
        ]);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'data' => $cart
        ], 201);
    }

    public function cartView($reg = null){
        if($reg == NULL){
            $cart = Cart::all();
            return response()->json([
                'message' => 'Get All Cart Products.',
                'data' => $cart
            ], 200);
        } else {
            $cart = Cart::where('reg', $reg)->get();
            return response()->json([
                'message' => 'Get All Cart Products.',
                'data' => $cart
            ], 200);
        }
    }

    public function cartRemove($reg, $id){
        // Logged-in user
        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)->where('product_id', $id)->where('reg', $reg)->first();

        if (!$cart) {
            return response()->json([
                'error' => 'Cart item not found'
            ], 404);
        } else {
            // Product fetch
            $product = Product::findOrFail($id);

            // Restore stock
            $product->increment('stock_quantity', $cart->quantity);

            // Delete cart item
            $cart->delete();

            return response()->json([
                'message' => 'Product remvoe from cart successfully',
                'data' => $cart
            ], 200);
        }
    }
}
