<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $password = (string) env('ADMIN_PASSWORD');
        if ($password === '') return $next($request);

        $user = (string) env('ADMIN_USER', 'admin');
        if (hash_equals($user, (string) $request->getUser()) && hash_equals($password, (string) $request->getPassword())) {
            return $next($request);
        }

        return response('Autenticación requerida.', 401, ['WWW-Authenticate' => 'Basic realm="La Brujula Admin"']);
    }
}
