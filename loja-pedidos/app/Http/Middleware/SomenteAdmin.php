<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SomenteAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->perfil === 'admin', 403, 'Acesso restrito a administradores.');

        return $next($request);
    }
}
