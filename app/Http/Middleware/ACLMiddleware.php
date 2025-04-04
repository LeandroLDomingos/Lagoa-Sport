<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class ACLMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user()->load('permissions', 'roles');
        $routeName = Route::currentRouteName();

        foreach ($user->roles as $role) {
            if ($role->name === 'admin') {
                return $next($request);
            }
        }

        $hasPermission = $user->permissions->contains('route_name', $routeName);

        if (!$hasPermission) {
            return redirect()->route('dashboard')
                ->with('flash.error', 'Você não tem permissão para acessar esta rota.');
        }
        
        
        

        $request->attributes->set('permissions', $user->permissions);

        return $next($request);
    }
}
