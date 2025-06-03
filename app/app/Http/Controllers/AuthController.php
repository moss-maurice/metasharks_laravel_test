<?php

namespace App\Http\Controllers;

use App\Contracts\AuthServiceInterface;
use App\Http\Requests\LoginAuthRequest;

class AuthController extends Controller
{
    public function login(LoginAuthRequest $request, AuthServiceInterface $service)
    {
        return $service->login($request);
    }
}
