<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserContact;
use App\Traits\ResponsesTrait;
use Illuminate\Http\Request;

class ContactsController extends Controller
{

    use ResponsesTrait;
    public function search_contacts(Request $request)
    {

        $current_user = $request->user();

        $users = User::where(function ($sql) use ($request) {
            $sql->where('email', 'like', "%{$request->get('value')}%")
                ->orWhere('user_name', 'like', "%{$request->get("value")}%");
        })
            ->where("id", "!=", $current_user->id)
            ->whereNull('deleted_at')
            ->paginate($request->get('per_page') ?? 15);

        return $this->success(['users' => $users]);
    }

    public function add_contact(Request $request)
    {
        $current_user = $request->user();

        $check_whether_contact_exists = UserContact
            ::where('user_id', $current_user->id)
            ->where("contact_id", $request->get('contact_id'))
            ->get();

        if (count($check_whether_contact_exists) >= 1) {
            return $this->fail(['message' => "User alredy has been added to contacts"]);
        }


        $contact = UserContact::create([
            'user_id' => $check_whether_contact_exists->id,
            'contact_id' => $request->get('contact_id'),
            'created_at' => date("Y-m-d H:i:s")
        ]);

        return $this->success(['contact' => $contact]);
    }
}
