<?php
namespace App\Http\Controllers\LighhouseGraphQl\Queries;

use App\Models\User;


class UserQuery
{
    //================================================================================================
    // protected $middleware = [
    //     CheckUser::class,
    //     UserPrimaryAddress::class,
    // ];
    //================================================================================================
    public function users($root, array $args, $context, $info){
        return User::all();
    }
    //================================================================================================
    public function test($root, array $args, $context, $info){
        // return 'test';
        return ['message'=>'test'];
    }
    
}