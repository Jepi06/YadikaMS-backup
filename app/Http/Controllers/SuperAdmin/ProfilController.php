<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    private function getUser(): User
    {
        return request()->attributes->get('superAdminUser');
    }

    public function edit()
    {
        $user = $this->getUser();
        return view('admin.profil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $this->getUser();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update(['name' => $request->name]);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $this->getUser();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false, // ← hapus flag setelah ganti password
        ]);

        return back()->with('status', 'Password berhasil diubah.');
    }

    public function updateAvatar(Request $request)
    {
        $user = $this->getUser();

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Hapus avatar lama
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('status', 'Foto profil berhasil diperbarui.');
    }

    public function deleteAvatar()
    {
        $user = $this->getUser();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('status', 'Foto profil berhasil dihapus.');
    }
}