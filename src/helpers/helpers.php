<?php 

namespace Lamplighter\Permissions;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Lamplighter\Permissions\Models\PermissionAction;
use Lamplighter\Permissions\Models\PermissionModule;

trait Admissibleness {

    public function permissions(){
        try{
            $route_name = Route::currentRouteName();
    
            $config_permissions = config('permissions')['messages'];
    
            $names = explode(".",$route_name);
    
            $search_module = (count($names) >= 2) ? $names[count($names)-2] : $names[0];
            
            $role_id = Auth::user()->role->id; //get role user
            $modulo = PermissionModule::where('prefix',$search_module)->first(); //get current module
            if(is_null($modulo)){ 
                throw new Exception($config_permissions['module_not_found'], 1);
            }
            
            if ($modulo->active != 1){
                throw new Exception($config_permissions['module_inactive'], 1);
            }
    
            $modulo_role = DB::table('permissions_roles_modules')
            ->where('module_id', $modulo->id)
            ->where('role_id', $role_id)
            ->first();
    
            if (is_null($modulo_role)){ 
                throw new Exception($config_permissions['permissions_denied'], 1);
            }
            
            $permissions = PermissionAction::whereHas('roles',function($roleModel) use ($role_id) {
                $roleModel->where('role_id',$role_id);
            })
            ->where('module_id',$modulo->id)
            ->select('name')
            ->get();
    
            return $permissions;
        }catch(Exception $e){
           return collect([]);
        }
    }

}