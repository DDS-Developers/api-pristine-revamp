<?php

namespace App\Http\Service;

use App\Http\Resources\LoginResource;
use App\User;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Http\Service\UploadFileService;
use App\Enums\UploadFileFolderEnum;

class AuthService
{

    public function login($req, $role)
    {
        $user = User::where(function ($q) use ($req) {
            $q->where('email', $req->username)
                ->orWhere('username', $req->username)
                ->orWhere('phone', $req->phone);
        })->where(['role' => $role])
            ->firstOrFail();

        if (Hash::check($req->password, $user->password)) {
            $token = auth()->attempt([
                'email' => $user->email,
                'password' => $req->password
            ]);
            $newCollection = collect($user)->merge([
                'token' => $token
            ]);
            return $newCollection;
        }
        throw new \Exception('Wrong password');
    }

    public function register($request, $role)
    {
        $avatar = NULL;
        if ($request->hasFile('avatar')) {
            $avatar = (new UploadFileService)->uploadImageFromFile($request->file('avatar'), UploadFileFolderEnum::UserAvatar)['path'];
        }
        $user = User::create([
            'username' => $request->username,
            'avatar' => $avatar,
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $role,
            'password' => Hash::make($request->password)
        ]);
        return $user;
    }

    public function updateProfile($request)
    {
        $user = User::where('id', auth()->user()->id)->firstOrFail();
        $avatar = NULL;
        if ($request->hasFile('avatar')) {
            $avatar = (new UploadFileService)->uploadImageFromFile($request->file('avatar'), UploadFileFolderEnum::UserAvatar)['path'];
            (new UploadFileService)->deleteFile($user->avatar);
        }
        $user->update([
            'username' => $request->username,
            'avatar' => $avatar,
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return $user->refresh();
    }
}
