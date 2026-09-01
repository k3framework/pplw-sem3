<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        abort_unless($request->user()?->role->name === $role, 403, 'Halaman ini tidak tersedia untuk role akun Anda.');

        return $next($request);
    }
}
