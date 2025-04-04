<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;  
use Symfony\Component\HttpFoundation\Response;

class ACLMiddleware
{
    // O construtor não precisa ser protected, a menos que tenha algo específico.
    public function __construct()
    {
        // Caso queira injeção de dependência do repositório, adicione aqui
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Recupera o usuário autenticado
        $user = Auth::user()->load('permissions', 'roles');

        // Recupera o nome da rota atual
        $routeName = Route::currentRouteName();

        // Verifica se o usuário tem um papel de "admin"
        foreach ($user->roles as $role) {
            if ($role->name === 'admin') {
                // Caso o usuário tenha a role "admin", ele tem acesso completo
                return $next($request);
            }
        }

        // Caso o usuário não tenha a role "admin", verificamos as permissões
        $hasPermission = $user->permissions->contains('route_name', $routeName);

        if (!$hasPermission) {
            // Se não tiver a permissão necessária, redireciona ou retorna uma resposta de erro
            return response()->json(['message' => 'Você não tem permissão para acessar esta rota.'], 403);
        }

        // Adiciona as permissões do usuário ao request (caso precise em alguma outra parte da aplicação)
        $request->attributes->set('permissions', $user->permissions);

        // Permite o próximo middleware/controle
        return $next($request);
    }
}
