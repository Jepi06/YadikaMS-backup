<?php

namespace App\Http\Controllers\Spmb\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('spmb.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('spmb')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $user = Auth::guard('spmb')->user();

        // Hanya admin SPMB (atau super admin) yang boleh masuk ke panel ini
        if (! $user->hasSpmbAccess()) {
            Auth::guard('spmb')->logout();
            return back()->withErrors(['email' => 'Akun Anda tidak memiliki akses ke sistem SPMB.']);
        }

        $request->session()->regenerate();

        // PERBAIKAN: super admin login dari sistem MANAPUN (PKL/SPMB/LMS)
        // selalu diarahkan ke Panel Super Admin, bukan ke dashboard
        // sistem tempat dia login.
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('spmb.admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('spmb')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('spmb.login');
    }
}