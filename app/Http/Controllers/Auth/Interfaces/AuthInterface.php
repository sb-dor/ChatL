<?php

namespace App\Http\Controllers\Auth\Interfaces;

use Illuminate\Http\Request;

interface AuthInterface
{
    public function check_auth(Request $request);

    public function register(Request $request);

    public function login(Request $request);

    public function google_auth(Request $request);
}
