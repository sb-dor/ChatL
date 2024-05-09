<?php

namespace App\Http\Controllers\Auth\Implementations;

use App\Http\Controllers\Auth\Interfaces\OtherAuthInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponsesTrait;
use Exception;
use Illuminate\Http\Request;

class OtherAuthController extends Controller implements OtherAuthInterface
{

    use ResponsesTrait;
    public function google_auth(Request $request)
    {
        try {
            $checkUser = User::where('email', $request->get('email'))->first();

            if (!$checkUser) {
                $checkUser = User::create([
                    'email' => $request->get('email'),
                    'google_id' => $request->get("google_id"),
                    'image_url' => $request->get('image_url'),
                ]);
            } else {
                User::where('email', $request->get('email'))->update([
                    'google_id' => $request->get("google_id"),
                    'image_url' => $request->get('image_url') ? $request->get('image_url') : $checkUser->image_url,
                ]);
            }

            $token = $checkUser->createToken('user_token')->plainTextToken;


            return $this->success([
                'user' => $checkUser,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            return $this->fail(['message' => $e->getMessage()]);
        }
    }

    public function facebook_auth(Request $request)
    {
        try {
            $checkUser = User::where('email', $request->get('email'))->first();

            if (!$checkUser) {
                $checkUser = User::create([
                    'email' => $request->get('email'),
                    'name' => $request->get('name'),
                    'image_url' => $request->get('image_url'),
                    'facebook_id' => $request->get("facebook_id"),
                ]);
            } else {
                User::where('email', $request->get('email'))->update([
                    'image_url' => $request->get('image_url') ? $request->get('image_url') : $checkUser->image_url,
                    'facebook_id' => $request->get("facebook_id"),
                ]);
            }

            $token = $checkUser->createToken('user_token')->plainTextToken;


            return $this->success([
                'user' => $checkUser,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            return $this->fail(['message' => $e->getMessage()]);
        }
    }
}
