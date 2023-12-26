<?php

namespace App\Http\Controllers;

use App\Models\User;

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
}
