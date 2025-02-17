<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile_about($id)
    {

        $user = User::where('id', $id)->first();

        if ($user) {
            return response()->json([
                'success' => false,
                'message' => "User does not exist",
            ]);
        }

        return response()->json([
            'success' => false,
            "user" => $user
        ]);
    }
}
