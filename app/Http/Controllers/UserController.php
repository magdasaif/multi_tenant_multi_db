<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request){
        // DB::setDefaultConnection('mysql');
        $tenant_id= explode('.',$request->getHost())[0];
        // dd(DB::getDefaultConnection());
        
        User::create([
            // 'tenant_id' => $tenant_id,
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);
        return 'user create successfully';
    }
}
