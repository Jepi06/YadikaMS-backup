<?php

// FILE: app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * PERBAIKAN: role_pkl bukan lagi kolom di tabel users (sekarang many-to-many
     * lewat modules/roles/user_role), jadi dicek lewat User::hasPklRole().
     * Ini juga otomatis mendukung user yang punya lebih dari satu role PKL
     * sekaligus (misal admin yang juga wali_kelas).
     *
     * Usage tidak berubah: ->middleware('role:wali_kelas,guru_bk')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('pkl')->user();

        if (!$user || !$user->hasPklRole(...$roles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
