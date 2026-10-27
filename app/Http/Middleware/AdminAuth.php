<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        return $request->session()->get('admin_authenticated') ? $next($request) : redirect()->guest(route('admin.login'));
    }
}
