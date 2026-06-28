<?php

namespace App\Http\Middleware;

use App\Models\Lahan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLahanOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->role === 'admin') {
            return $next($request);
        }

        $lahanId = $request->route('lahan') ?? $request->route('id');

        if ($lahanId) {
            $lahan = Lahan::find($lahanId);
            if (!$lahan || $lahan->petugas_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke lahan ini');
            }
        }

        return $next($request);
    }
}
