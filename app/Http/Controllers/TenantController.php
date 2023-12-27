<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class TenantController extends Controller
{
    public function index(Request $request){
        //==============================================================
        // $tenant=$request->domain;
        // $tenant1 = Tenant::create(['id' => $tenant]);
        // $tenant1->domains()->create(['domain' => $tenant.'.localhost']);
        // return 'tenant created sucessfully';
        //==============================================================
        // $tenant_db_path=[database_path('migrations/sidalih')];
        $project_name=$request->project;//'eradonline';
        $tenant=$request->domain;
        //==============================================================
        //get all folders inside selected project
        // $directories = array_map('basename', File::directories(database_path('migrations/'.$project_name)));
        $directories = array_map('class_basename', File::directories(database_path('migrations/'.$project_name)));
        $tenant_dbs_path=[];
        if(count($directories)>0){
            //here we need to loop inside project folders and create db for each one
            foreach($directories as $dir){
                $tenant_dbs_path[]=database_path('migrations/'.$project_name.'/'.$dir);
            }
        }else{
            $tenant_dbs_path[]=database_path('migrations/'.$project_name);
        }
        //==============================================================
        // $tenant_db_path=[database_path('migrations/'.$project_name)];
        $tenant_db_path=$tenant_dbs_path;
        //==============================================================
        //update config data
        Config::set('tenancy.database.prefix',$project_name.'_');
        Config::set('tenancy.migration_parameters.--path',$tenant_db_path);
        //==============================================================
        $tenant1 = Tenant::create(['id' => $tenant]);
        $tenant1->domains()->create(['domain' => $tenant.'.localhost']);
        return 'tenant created sucessfully';

        //==============================================================
        //try to create new tenant,domain,db for each dir
        if(count($directories)>0){
            //here we need to loop inside project folders and create db for each one
            foreach($directories as $dir){
                
                Config::set('tenancy.migration_parameters.--path',[database_path('migrations/'.$project_name.'/'.$dir)]);

                $tenant1 = Tenant::create(['id' => $tenant.'_'.$dir]);
                $tenant1->domains()->create(['domain' => $tenant.'_'.$dir.'.localhost']);
            }
        }else{

            Config::set('tenancy.migration_parameters.--path',[database_path('migrations/'.$project_name)]);


            $tenant1 = Tenant::create(['id' => $tenant]);
            $tenant1->domains()->create(['domain' => $tenant.'.localhost']);
        }
        return 'tenant created sucessfully';
        //==============================================================

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
