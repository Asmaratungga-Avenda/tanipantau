<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RedirectIfAuthenticated — pengganti 'guest' middleware default
 *
 * Default Laravel guest middleware redirect ke route 'login',
 * tapi di project ini route 'login' = /admin/login, sehingga
 * menyebabkan infinite redirect loop jika user sudah login.
 *
 * Middleware ini redirect ke /admin/dashboard jika sudah login.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect('/admin/dashboard');
            }
        }

        return $next($request);
    }
}
