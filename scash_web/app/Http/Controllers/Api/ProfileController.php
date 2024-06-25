<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function search(Request $request, $role_id =3)
    {
        
            $keyword = $request->keyword;
            $users = User::select('id','name','email')->where('role_id',$role_id)->where(function($q) use($keyword){
                $q->where('name', ' LIKE', '%' . $keyword . '%')
                ->orWhere('email', 'LIKE', '%' . $keyword . '%');
            })->get();
           
            return $this->sendResponse($users, 'Users List.');
    }
}
