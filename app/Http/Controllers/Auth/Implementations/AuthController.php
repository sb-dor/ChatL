<?php

namespace App\Http\Controllers\Auth\Implementations;

use App\Http\Controllers\Auth\Interfaces\AuthInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponsesTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller implements AuthInterface
{

    use ResponsesTrait;
    public function check_auth(Request $request)
    {

        $user = auth("sanctum")->user();

        if (!$user) return $this->fail();

        return $this->success(['user' => $user]);
    }

    public function register(Request $request)
    {
        try {
            $user = User::where('email', $request->get('email'))->first();

            if ($user) {
                return $this->fail([
                    'message' => "There is a user which is already exists with this email!"
                ]);
            }

            $user = User::where('user_name', $request->get('user_name'))->first();

            if ($user) {
                return $this->fail([
                    'message' => "There is a user which is already exists with this user name!"
                ]);
            }

            $user = User::create([
                'user_name' => $request->get('user_name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'created_at' => date("Y-m-d H:i:s")
            ]);

            $token = $user->createToken('user_token')->plainTextToken;

            return $this->success([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            return $this->fail(['message' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $user = User
            ::where(function ($sql) use ($request) {
                $sql->where('email', $request->get('email_or_username'))
                    ->orWhere('user_name', $request->get('email_or_username'));
            })->first();

        if (!$user) {
            return $this->fail([
                'message' => "There is no user with this Email or User name"
            ]);
        }


        if (!Hash::check($request->get('password'), $user->password)) {
            return $this->fail([
                'message' => "There is a user which is already exists with this user name!"
            ]);
        }

        $user = User::create([
            'user_name' => $request->get('user_name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = $user->createToken('user_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
