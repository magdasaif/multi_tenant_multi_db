<?php
namespace App\Http\Controllers\LighhouseGraphQl\Queries\central;

use App\Models\User;
use App\Models\Tenant;
use Stancl\Tenancy\Facades\Tenancy;


class UserQuery
{

    //================================================================================================
    // protected $middleware = [
    //     \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
    //     \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    // ];
    //================================================================================================
    // public function allUsers($root, array $args, $context, $info){
    //     return User::all();
    // }
    //================================================================================================
    public function tenantUsers($root, array $args, $context, $info){
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
        return $users;
        return response()->json($users);
    }
    //================================================================================================
    // public function test($root, array $args, $context, $info){
    //     // return 'test';
    //     return ['message'=>'test'];
    // }
    
}