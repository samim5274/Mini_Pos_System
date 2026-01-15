<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function payment(Request $request){
        
         $request->validate([
            'reg'      => 'required|string',            
            'name'     => 'required|string',
            'phone'    => 'required|string',
            'address'  => 'required|string',            
            'discount' => 'nullable|numeric|min:0',
            'pay'      => 'required|numeric|min:0',
        ]);

        $reg    = $request->reg;
        
        $order = Order::where('reg', $reg)->where('user_id', Auth::id())->first();
        if (!$order) {
            return response()->json([
                'error' => 'Order not found',
                'data' => $order
            ], 404);
        }
                
        $order->status = "Paid";
        $order->update();
        
        $total    = $order->total;
        $discount = $request->discount ?? 0;
        $payable  = $total - $discount;
        $pay      = $request->pay;
        $due      = 0;
        $return   = 0;
        $duestatus = 'paid';
        
        
        if ($pay < $payable) {
            $due = $payable - $pay;
            $duestatus = 'due';
        } elseif ($pay > $payable) {
            $return = $pay - $payable;
        }
        
        $payments = Payment::where('reg', $reg)->where('userId', Auth::id())->first();
        if ($payments) {
            return response()->json([
                'error' => 'Payment already done.',
                'payment' => $payments
            ], 404);
        }

        $payment = new Payment();
        $payment->reg       = $request->reg;
        $payment->date      = now()->toDateString();
        $payment->name      = $request->name;
        $payment->phone     = $request->phone;
        $payment->address   = $request->address;
        $payment->total     = $total;
        $payment->discount  = $discount;
        $payment->payable   = $payable;
        $payment->pay       = $pay;
        $payment->due       = $due;
        $payment->return    = $return;
        $payment->duestatus = $duestatus;
        $payment->status    = 'completed';
        $payment->userId    = auth()->id();
        $payment->save();

        return response()->json([
            'message' => 'Payment successful',
            'data'    => $payment
        ], 201);
    }
}
