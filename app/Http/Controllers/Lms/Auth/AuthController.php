<?php

namespace App\Http\Controllers\Lms\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Lms\DashboardPublicController;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('lms.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'], // isinya boleh email ATAU NIS
            'password' => ['required'],
        ]);

        $login = trim($data['login']);
        $remember = $request->boolean('remember');

        if (str_contains($login, '@')) {
            // ── Login pakai EMAIL — pola biasa lewat Auth::attempt() ──
            $berhasil = Auth::guard('lms')->attempt([
                'email' => $login,
                'password' => $data['password'],
            ], $remember);

            if (! $berhasil) {
                return back()
                    ->withErrors(['login' => 'Email/NIS atau password salah.'])
                    ->onlyInput('login');
            }
        } else {
            // ── Login pakai NIS — cari siswa pemilik NIS ini, ambil
            // user_id-nya, baru cek password manual (Auth::attempt gak
            // bisa langsung dipakai karena kolom login di tabel users
            // cuma 'email', bukan 'nis').
            $siswa = Siswa::where('nis', $login)->first();

            if (! $siswa || ! $siswa->user_id) {
                return back()
                    ->withErrors(['login' => 'NIS tidak ditemukan atau belum terhubung ke akun LMS. Hubungi admin.'])
                    ->onlyInput('login');
            }

            $user = User::find($siswa->user_id);

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                return back()
                    ->withErrors(['login' => 'Email/NIS atau password salah.'])
                    ->onlyInput('login');
            }

            Auth::guard('lms')->login($user, $remember);
        }

        $user = Auth::guard('lms')->user();

        if (! $user->hasLmsAccess()) {
            Auth::guard('lms')->logout();

            return back()
                ->withErrors(['login' => 'Akun Anda tidak memiliki akses ke modul LMS.'])
                ->onlyInput('login');
        }

        $request->session()->regenerate();

        return DashboardPublicController::redirectToDashboard($user);
    }

    public function logout(Request $request)
    {
        Auth::guard('lms')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('lms');
    }
}