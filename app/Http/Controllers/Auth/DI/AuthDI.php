<?php

namespace App\Http\Controllers\Auth\DI;

use App\Http\Controllers\Auth\Implementations\AuthController;
use App\Http\Controllers\Auth\Interfaces\AuthInterface;
use Illuminate\Http\Request;

class AuthDI
{
    private AuthInterface $auth;

    public function __construct()
    {
        $this->auth = new AuthController();
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
}
