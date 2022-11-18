<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

use App\Http\Service\AuthService;
use Illuminate\Support\Facades\Route;
use DB;
use App\Enums\RoleEnum;
use App\Http\Resources\LoginResource;
use App\Http\Resources\RegisterResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Requests\UpdateProfileRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct()
    {
        $this->authService = new AuthService;
    }

    public function login(LoginRequest $request)
    {
        DB::beginTransaction();
        try {
            $route = Route::currentRouteName();
            $accessRole = ucwords(explode('.', $route)[0]);
            $accessRole = RoleEnum::getData()[$accessRole];
            $login =  $this->authService->login($request, $accessRole);

            DB::commit();
            return new LoginResource($login);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->authService->register($request, RoleEnum::Customer);
            DB::commit();
            return new RegisterResource($user);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function profile(Request $request)
    {
        try {
            return new UserProfileResource(auth()->user());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->authService->updateProfile($request);
            DB::commit();
            return new RegisterResource($user);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->logout(true);
            return response()->json([
                'code' => 200,
                'message' => 'Success'
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
