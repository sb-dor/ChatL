<?php

namespace App\Http\Controllers\Auth\Interfaces;

use Illuminate\Http\Request;

interface OtherAuthInterface
{
    public function google_auth(Request $request);

    public function facebook_auth(Request $request);
}
