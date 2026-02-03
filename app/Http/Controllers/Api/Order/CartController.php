<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Services\RegGenerator;

class CartController extends Controller
{
    public function addCart(Request $request, $id){

        // Logged-in user
        $user = Auth::user();

        // Validation
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        return DB::transaction(function () use ($request, $id, $user) {

            // Product fetch
            $product = Product::lockForUpdate()->findOrFail($id);

            // Stock check
            if ($request->quantity > $product->stock_quantity) {
                return response()->json([
                    'error' => 'Not enough stock available'
                ], 400);
            }

            // Generate reg (same day + same user)
            $userId = $user->id;
            $reg = RegGenerator::generateOrderReg($userId);
            
            // Check existing cart
            $cart = Cart::where('user_id', $userId)
                ->where('product_id', $product->id)
                ->where('reg', $reg)
                ->lockForUpdate()
                ->first();

            if ($cart) {
                $newQty = $cart->quantity + $request->quantity;

                if ($request->quantity > $product->stock_quantity) {
                    return response()->json([
                        'error' => 'Stock limit exceeded'
                    ], 400);
                }

                
                $cart->update([
                    'quantity'  => $newQty,
                    // 'price'     => $product->price * $newQty, // যদি price=total হয়
                    'price'     => $product->price, // যদি price=unit হয়
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

            // decrement stock safely
            $product->decrement('stock_quantity', $request->quantity);

            return response()->json([
                'message' => 'Product add to cart successfully',
                'data' => $cart,
                'product' => $product->fresh(),
            ], 200);

        });
    }

    public function cartCount()
    {
        $userId = Auth::id();

        $todayPrefix = now()->format('Ymd') . $userId;

        $reg = Cart::where('user_id', $userId)
            ->where('reg', 'like', $todayPrefix.'%')
            ->latest('id')
            ->value('reg');

        $count = 0;
        if ($reg) {
            $count = Cart::where('user_id', $userId)
                ->where('reg', $reg)
                ->count();
        }

        return response()->json(['count' => $count]);
    }

    public function cartView(Request $request){
        
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized user',
                'reg'     => null,
                'data'    => [],
            ], 401);
        }
        
        $reg = RegGenerator::generateOrderReg($userId);
        
        $items = Cart::with(['product','tenant','user'])
            ->where('user_id', $userId)
            ->where('reg', $reg)
            ->get();

        return response()->json([
            'message' => 'Cart items',
            'reg' => $reg,     
            'data' => $items
        ], 200);
    }

    public function cartRemove($reg, $id){
        // Logged-in user
        $user = Auth::user();

        return DB::transaction(function () use ($user, $reg, $id) {

            $cart = Cart::where('user_id', $user->id)->where('product_id', $id)->where('reg', $reg)->lockForUpdate()->first();

            if (!$cart) {
                return response()->json([
                    'error' => 'Cart item not found'
                ], 404);
            } else {
                // Product fetch
                $product = Product::where('id', $id)->lockForUpdate()->first();

                // Restore stock
                $product->increment('stock_quantity', $cart->quantity);

                // Delete cart item
                $cart->delete();

                return response()->json([
                    'message' => 'Product remvoe from cart successfully',
                    'data' => $cart
                ], 200);
            }

        });
    }

    public function updateQty(Request $request, $reg, $productId){
        $data = $request->validate([
            'quantity' => ['required','integer','min:1'],
        ]);

        return DB::transaction(function () use ($data, $reg, $productId) {

            // 1) cart row lock
            $cart = Cart::where('reg', $reg)->lockForUpdate()->where('product_id', $productId)->firstOrFail();

            $oldQty = (int) $cart->quantity;
            $newQty = (int) $data['quantity'];

            if ($newQty === $oldQty) {
                return response()->json([
                    'message' => 'No change',
                    'quantity' => $cart->quantity,
                ]);
            }

            $diff = $newQty - $oldQty;
            // diff > 0 => customer more qty => stock কমবে
            // diff < 0 => customer less qty => stock বাড়বে

            // 2) product row lock
            $product = Product::lockForUpdate()->where('id', $productId)->firstOrFail();
            
            // 3) stock check + update
            if ($diff > 0) {
                // extra qty নিতে হলে stock যথেষ্ট থাকতে হবে
                if ($product->stock_quantity < $diff) {
                    return response()->json([
                        'message' => 'Out of Stock.',
                        'available_stock' => $product->stock_quantity,
                    ], 422);
                }
                $product->stock_quantity = $product->stock_quantity - $diff;
            } else {
                // qty কমালে stock ফেরত যাবে
                $product->stock_quantity = $product->stock_quantity + abs($diff);
            }

            // 4) save both
            $cart->quantity = $newQty;

            $product->save();
            $cart->save();

            return response()->json([
                'message' => 'Quantity updated',
                'quantity' => $cart->quantity,
                'stock_quantity' => $product->stock_quantity,
            ]);
        });
    }
}
