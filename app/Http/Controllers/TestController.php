<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
// use Stancl\Tenancy\Tenancy;
use Stancl\Tenancy\Facades\Tenancy;

class TestController extends Controller
{
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
    //=========================================================
    public function users(){
        //=========================================================
            // GET /users?filter[include][posts]
            // =author&filter[limit]=2
        //=========================================================
        $query = User::query();
        $filter = request()->input('filter');
        $relations= $filter['include'];
        
        // return ($relations);

        foreach((array)$relations as $key=>$value){
            // return $key;

            // $rel= "`".$key.':'.$value."`";
            // $query->with($rel); 


            // $query->with('posts:id'); 
            $query->with($key); 
           
            // $query->with([$key => function ($query) use($value){
            //     $query->select($value);
            //     // dd($query);
            // }]);

        }
        // return 'fff';
        $users = $query->get();
        return response()->json($users);
        //=========================================================
        // $filter = request()->input('filter');
        // $query = User::query();
        // // Include related posts
        // if (in_array('posts', $filter['include'] ?? [])) {
        //     $query->with('posts');
        // }
        // // Include related passports
        // if (in_array('passports', $filter['include'] ?? [])) {
        //     $query->with('passports');
        // }
        // $members = $query->get();
        // return response()->json($members);
        //=========================================================

    }
    //=========================================================
    public function allTenantUsers(){
        // return tenant();
        // Get the subdomain tenants
        $tenants = Tenant::all();

        $users = [];
        
        // $nn=new Tenancy();
        // return $nn->model();
        foreach ($tenants as $tenant) {
            // Switch to the subdomain tenant's database connection
            
            Tenancy::initialize($tenant);

            // Retrieve users from the subdomain tenant's database
            $tenantusers = User::all();

            // Merge the users into the main users array
            $users = array_merge($users, $tenantusers->toArray());
        }
        // return $users;
        return response()->json($users);
    }
}
