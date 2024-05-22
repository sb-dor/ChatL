<?php

namespace App\Http\Controllers\Auth\DI;

use App\Http\Controllers\Auth\Implementations\AuthController;
use App\Http\Controllers\Auth\Implementations\OtherAuthController;
use App\Http\Controllers\Auth\Interfaces\AuthInterface;
use App\Http\Controllers\Auth\Interfaces\OtherAuthInterface;
use Illuminate\Http\Request;

class AuthDI
{
    private AuthInterface $auth;
    private OtherAuthInterface $otherAuthInterface;

    public function __construct()
    {
        $this->auth = new AuthController();
        $this->otherAuthInterface = new OtherAuthController();
    }

    public function check_auth(Request $request)
    {
        return $this->auth->check_auth($request);
    }

    public function register(Request $request)
    {
        return $this->auth->register($request);
    }

    public function login(Request $request)
    {
        return $this->auth->login($request);
    }

    public function google_auth(Request $request)
    {
        return $this->otherAuthInterface->google_auth($request);
    }

    public function facebook_auth(Request $request)
    {
        return $this->otherAuthInterface->facebook_auth($request);
    }

    public function logout(Request $request)
    {
        return $this->auth->logout($request);
    }
}
