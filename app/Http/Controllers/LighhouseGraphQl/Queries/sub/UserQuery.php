<?php
namespace App\Http\Controllers\LighhouseGraphQl\Queries\sub;

use App\Models\User;
use App\Models\Tenant;
use Stancl\Tenancy\Facades\Tenancy;
use App\Http\Controllers\Controller;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


class UserQuery /*extends Controller*/
{
    // public function __construct()
    // {
    //     // Assign to ALL methods in this Controller
    //     $this->middleware('tenancy');
    // }

    // use InitializeTenancyByDomain,PreventAccessFromCentralDomains;
    //================================================================================================
    // protected $middleware = [
    //     \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
    //     \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    // ];
    //================================================================================================
    public function allUsers($root, array $args, $context, $info){
        return User::all();
        // return  $user=User::all(); 
        // Retrieve subdomain from the request or context, depending on how you handle tenancy
        $hostParts = explode('.', request()->getHost());
        $subdomain = $hostParts[0]; // Extract subdomain from the host
        Tenancy::initialize($subdomain); // Initialize tenancy for the subdomain
        $user = User::all();
        return $user;

    }
    //================================================================================================
    // public function tenantUsers($root, array $args, $context, $info){
    //     // return tenant();
    //     // Get the subdomain tenants
    //     $tenants = Tenant::all();

    //     $users = [];
        
    //     // $nn=new Tenancy();
    //     // return $nn->model();
    //     foreach ($tenants as $tenant) {
    //         // Switch to the subdomain tenant's database connection
            
    //         Tenancy::initialize($tenant);

    //         // Retrieve users from the subdomain tenant's database
    //         $tenantusers = User::all();

    //         // Merge the users into the main users array
    //         $users = array_merge($users, $tenantusers->toArray());
    //     }
    //     return $users;
    //     return response()->json($users);
    // }
    //================================================================================================
    public function testDirective($root, array $args, $context, $info){
        // return 'test';
        return $args['name'];
        return ['message'=>'test'];
    }
    
}