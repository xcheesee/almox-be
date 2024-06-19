<?php

namespace App\Http\Controllers;

use App\Models\local_users;
use \App\Http\Resources\local_users as localUser;
use Illuminate\Http\Request;

class LocalUsersController extends Controller
{
    public function add_user_local(Request $request)
    {
        $localUser = local_users::pluck('user_id', 'local_id');

        return $localUser;
    }
    
    public function LocalUsuarios($id){
        $bases = local_users::where('user_id', $id)->get();
        
        return localUser::collection($bases);
    }
}