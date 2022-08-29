<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Http\Response\AuthTransformer;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where([
                'email' => $request->email,
            ])->firstOrFail();

            if (Hash::check($request->password, $user->password)) {
                $token = auth()->attempt([
                    'email' => $user->email,
                    'password' => $request->password
                ]);

                return AuthTransformer::login($user, $token);
            }

            throw new \Exception('Wrong Password');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
