<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;

class ProductController extends Controller
{
    public function show($id = null){
        if($id == NULL){
            $data = Product::all();
            return response()->json([
                'message' => 'Get All Products.',
                'data' => $data
            ], 200);
        }else {
            $data = Product::findOrFail($id);
            return response()->json([
                'message' => 'Get Product Detail.',
                'data' => $data
            ], 200);
        }
    }

    public function store(Request $request)
    {     
        // Validation
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|exists:tenants,id', // Tenant must exist
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $tenantId = $request->tenant_id;

        // Tenant-wise SKU uniqueness check
        $existingProduct = Product::where('tenant_id', $tenantId)
            ->where('sku', $request->sku)
            ->first();

        if ($existingProduct) {
            return response()->json([
                'error' => 'SKU already exists for this tenant'
            ], 409); // Conflict
        }

        // Create Product
        $product = Product::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
        ]);

        return response()->json([
            'message' => 'Product Created Successfully',
            'data' => $product
        ], 201);
    }

    // Update existing product
    public function update(Request $request, $tenantId, $productId)
    {
        // Tenant-wise product fetch
        $product = Product::where('tenant_id', $tenantId)
            ->where('id', $productId)
            ->firstOrFail();

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'low_stock_threshold' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // SKU tenant-wise unique check
        if ($request->has('sku') && $request->sku !== $product->sku) {
            $existingProduct = Product::where('tenant_id', $tenantId)
                ->where('sku', $request->sku)
                ->first();

            if ($existingProduct) {
                return response()->json([
                    'error' => 'SKU already exists for this tenant'
                ], 409);
            }
        }

        // Update product
        $product->update($request->only([
            'name',
            'sku',
            'price',
            'stock_quantity',
            'low_stock_threshold'
        ]));

        return response()->json([
            'message' => 'Product Updated Successfully',
            'data' => $product
        ], 200);
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }
        $product->delete();

        return response()->json([
            'message' => 'Product Deleted Successfully'
        ], 200);
    }
}
