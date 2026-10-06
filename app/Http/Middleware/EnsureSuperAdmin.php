<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::resolveAnyGuardUser();

        if (!$user) {
            return redirect('/')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        if (!$user->isSuperAdmin()) {
            abort(403, 'Halaman ini khusus Super Admin.');
        }

        $request->attributes->set('superAdminUser', $user);

        // Redirect ke profil jika must_change_password
        // Kecuali kalau sudah di halaman profil (hindari redirect loop)
        if ($user->must_change_password && !$request->routeIs('admin.profil.*')) {
            return redirect()->route('admin.profil.edit')
                ->withErrors(['password' => 'Anda menggunakan password default. Harap ganti password sebelum melanjutkan.']);
        }

        return $next($request);
    }
}