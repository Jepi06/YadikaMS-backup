<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sama seperti RoleMiddleware (untuk PKL) tapi dicek lewat guard 'lms'
 * dan role modul 'lms'. Usage: ->middleware('role.lms:guru,admin')
 */
class RoleLmsMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('lms')->user();

        if (!$user || !$user->hasLmsRole(...$roles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
