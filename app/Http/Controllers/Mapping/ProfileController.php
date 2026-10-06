<?php

namespace App\Http\Controllers\Mapping;

use App\Http\Controllers\Concerns\UpdatesSharedProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use UpdatesSharedProfile;

    public function edit()
    {
        return view('Mapping.profile');
    }

    public function update(Request $request)
    {
        return $this->doUpdateProfile($request, 'pkl');
    }

    public function updatePassword(Request $request)
    {
        return $this->doUpdatePassword($request, 'pkl');
    }

    public function updateAvatar(Request $request)
    {
        return $this->doUpdateAvatar($request, 'pkl');
    }

    public function deleteAvatar()
    {
        return $this->doDeleteAvatar('pkl');
    }
}
