<?php

namespace App\Http\Middleware;

use App\Models\Kunjungan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckKunjunganOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->role === 'admin') {
            return $next($request);
        }

        $kunjunganId = $request->route('kunjungan') ?? $request->route('id');

        if ($kunjunganId) {
            $kunjungan = Kunjungan::find($kunjunganId);
            if (!$kunjungan || $kunjungan->petugas_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke kunjungan ini');
            }
        }

        return $next($request);
    }
}
