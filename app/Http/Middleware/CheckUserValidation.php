<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserValidation
{
    /**
     * Rotas que usuários pendentes podem acessar (como envio de documentos e logout).
     */
    protected $allowedRoutes = [
        'profile.edit',
        'profile.update',
        'profile.destroy',
        'password.edit',
        'password.update',
        'documents.create',
        'documents.store',
        'documents.is_analising',
        'appearance',
        'logout',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // Se o usuário não estiver autenticado, não precisa validar o status
        if (!$user) {
            return $next($request);
        } else {
            $user->load('permissions', 'roles');
        }


        foreach ($user->roles as $role) {
            if (!$role->level > 2) {
                return $next($request);
            }
        }


        // Se o usuário estiver pendente ou aguardando validação
        if (in_array($user->status, ['pending'])) {
            // Verifica se a rota atual não está na lista de rotas permitidas
            if (!in_array($request->route()->getName(), $this->allowedRoutes)) {
                return redirect()->route('documents.index')
                    ->with('flash.error', 'Seu cadastro está pendente. Por favor, envie seus documentos para validação.');
            }
        }

        if (in_array($user->status, ['is_analising'])) {
            // Verifica se a rota atual não está na lista de rotas permitidas
            if (!in_array($request->route()->getName(), $this->allowedRoutes)) {
                return redirect()->route('documents.is_analising')
                    ->with('flash.error', 'Seu cadastro está sendo analisado. Por favor, aguarde a validação.');
            }
        }

        return $next($request);

    }
}
