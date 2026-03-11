<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            // Se for admin tentando acessar rota de client, manda pro admin dashboard
            if ($request->user() && $request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            // Se for cliente tentando acessar rota admin, manda pra carteira
            if ($request->user() && $request->user()->role === 'client') {
                return redirect()->route('client.wallet');
            }

            abort(403, 'Acesso Negado');
        }

        return $next($request);
    }
}
