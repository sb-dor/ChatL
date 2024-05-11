<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ResponsesTrait;
use Illuminate\Http\Request;

class ContactsController extends Controller
{

    use ResponsesTrait;
    public function search_contacts(Request $request)
    {
        $users = User::where(function ($sql) use ($request) {
            $sql->where('email', 'like', "%{$request->get('value')}%")
                ->orWhere('user_name', 'like', "%{$request->get("value")}%");
        })
            ->whereNull('deleted_at')
            ->paginate($request->get('per_page') ?? 15);

        return $this->success(['users' => $users]);
    }

    public function add_contact(Request $request)
    {
    }
}
