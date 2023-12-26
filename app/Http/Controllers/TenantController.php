<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request){
        $tenant=$request->domain;
        $tenant1 = Tenant::create(['id' => $tenant]);
        $tenant1->domains()->create(['domain' => $tenant.'.localhost']);
        return 'tenant created sucessfully';
    }

    //=========================================================
    public function test_rest($user){
        if($user=User::find($user)){
            $fields = request()->input('fields', 'id,name');
            $fieldsArray = explode(',', $fields);
            $user = $user->only($fieldsArray);
            return response()->json($user);
        }
        return 'no user found';
    }
}
