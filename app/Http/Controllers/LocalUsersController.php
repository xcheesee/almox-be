<?php

namespace App\Http\Controllers;

use App\Models\local_users;
use Illuminate\Http\Request;

class LocalUsersController extends Controller
{
    public function add_user_local(Request $request)
    {
        $localUser = local_users::pluck('user_id', 'local_id');

        return $localUser;
    }
}
