<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateLms
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('lms')->user();

        if (!$user) {
            return redirect()->route('lms.login');
        }

        if (!$user->hasLmsAccess()) {
            Auth::guard('lms')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('lms.login')
                ->withErrors(['email' => 'Akun Anda tidak memiliki akses ke modul LMS.']);
        }

        if ($user->must_change_password && !$request->routeIs('lms.profil.*')) {
            return redirect()->route('lms.profil.edit')
                ->withErrors(['password' => 'Harap ganti password default sebelum melanjutkan.']);
        }

        return $next($request);
    }
}