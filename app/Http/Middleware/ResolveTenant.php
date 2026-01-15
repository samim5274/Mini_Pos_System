<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->header('X-Tenant-ID');

        if (!$tenantId) {
            return response()->json([
                'error' => 'Tenant ID missing'
            ], 400);
        }

        // store tenant globally for this request
        app()->instance('tenant_id', $tenantId);

        return $next($request);
    }
}
