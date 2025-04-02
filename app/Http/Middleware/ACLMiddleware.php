<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class ACLMiddleware
{
    public function __construct(private UserRepository $userRepository)
    {
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // Obtém o usuário autenticado
    
        // Se o usuário for null (não autenticado), retorna erro
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
    
            abort(403, 'Unauthenticated');
        }
    
        $routeName = Route::currentRouteName();
    
        if (!$this->userRepository->hasPermissions($user, $routeName)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not authorized'], 403);
            }
    
            abort(403, 'Not authorized');
        }
    
        // Adiciona as permissões do usuário ao request (para Inertia)
        $request->attributes->set('permissions', $this->userRepository->getUserPermissions($user));
    
        return $next($request);
    }
    
}
