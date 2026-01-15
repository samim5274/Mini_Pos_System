<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;

class ReportController extends Controller
{
    public function dailySaleSummary(Request $request){

        // Tenant ID from request header
        $tenantId = $request->header('X-Tenant-ID');

        if(!$tenantId){
            return response()->json([
                'success' => false,
                'message' => 'Tenant ID is required'
            ], 400);
        }

        // Date filter (today)
        $date = $request->input('date', now()->toDateString());

        $summary = Order::where('tenant_id', $tenantId)
                        ->where('status', 'Paid') // only consider paid orders
                        ->whereDate('date', $date)
                        ->selectRaw('COUNT(*) as total_orders, SUM(total) as total_sales')
                        ->first();

        return response()->json([
            'success' => true,
            'date' => $date,
            'total_orders' => $summary->total_orders ?? 0,
            'total_sales' => $summary->total_sales ?? 0,
        ]);
    }

    public function topSellingProducts(Request $request)
    {
        // Tenant ID from header
        $tenantId = $request->header('X-Tenant-ID');

        if(!$tenantId){
            return response()->json([
                'success' => false,
                'message' => 'Tenant ID is required'
            ], 400);
        }
        
        $topProducts = Cart::select('product_id', DB::raw('SUM(quantity) as total_quantity_sold'))      
                ->with('product:id,name,sku,price')           
                ->where('tenant_id', $tenantId)
                ->groupBy('product_id')
                ->orderByDesc('total_quantity_sold')->limit(5)->get();

        return response()->json([
            'success' => true,
            'top_products' => $topProducts
        ]);
    }

    public function lowStock(Request $request){
        // Tenant ID from header
        $tenantId = $request->header('X-Tenant-ID');

        if(!$tenantId){
            return response()->json([
                'success' => false,
                'message' => 'Tenant ID is required'
            ], 400);
        }

        // Fetch products where stock is low
        $lowStockProducts = Product::where('tenant_id', $tenantId)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->get();

        return response()->json([
            'success' => true,
            'total_low_stock' => $lowStockProducts->count(),
            'low_stock_products' => $lowStockProducts
        ]);
    }
}
