<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization', '');
        
        // Check for admin token
        if (str_contains($authHeader, 'admin_token_')) {
            // Create virtual admin user for this request
            $admin = new User([
                'id' => 0,
                'name' => 'Admin User',
                'email' => env('ADMIN_EMAIL'),
                'role' => 'admin'
            ]);
            
            // Set the admin as authenticated user
            $request->setUserResolver(function () use ($admin) {
                return $admin;
            });
            
            return $next($request);
        }
        
        // For regular users, use default Sanctum auth
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        return $next($request);
    }
}