<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        /** @var User $user */
        $user = Auth::user();
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses Ditolak');
        }

        return $next($request);
    }
}