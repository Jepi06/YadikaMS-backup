<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

trait UpdatesSharedProfile
{
    protected function doUpdateProfile(Request $request, string $guard)
    {
        $user = Auth::guard($guard)->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update(['name' => $request->name]);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    protected function doUpdatePassword(Request $request, string $guard)
    {
        $user = Auth::guard($guard)->user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return back()->with('status', 'Password berhasil diubah.');
    }

    protected function doUpdateAvatar(Request $request, string $guard)
    {
        $user = Auth::guard($guard)->user();

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $old = $user->avatar;

        $path = $request->file('avatar')->store('avatars', 'public');

        if (! $path) {
            return back()->withErrors(['avatar' => 'Gagal menyimpan foto, coba lagi.']);
        }

        $user->update(['avatar' => $path]);

        if ($old && $old !== '0') {
            Storage::disk('public')->delete($old);
        }

        return back()->with('status', 'Foto profil berhasil diperbarui.');
    }

    protected function doDeleteAvatar(string $guard)
    {
        $user = Auth::guard($guard)->user();

        if ($user->avatar && $user->avatar !== '0') {
            $deleted = Storage::disk('public')->delete($user->avatar);

            if (! $deleted) {
                \Log::warning('Gagal menghapus avatar', ['path' => $user->avatar]);
            }
        }

        $user->update(['avatar' => null]);

        return back()->with('status', 'Foto profil berhasil dihapus.');
    }
}
