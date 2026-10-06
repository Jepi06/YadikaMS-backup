<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Concerns\UpdatesSharedProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use UpdatesSharedProfile;

    public function edit()
    {
        return view('lms.profile');
    }

    public function update(Request $request)
    {
        return $this->doUpdateProfile($request, 'lms');
    }

    public function updatePassword(Request $request)
    {
        return $this->doUpdatePassword($request, 'lms');
    }

    public function updateAvatar(Request $request)
    {
        return $this->doUpdateAvatar($request, 'lms');
    }

    public function deleteAvatar()
    {
        return $this->doDeleteAvatar('lms');
    }
}
