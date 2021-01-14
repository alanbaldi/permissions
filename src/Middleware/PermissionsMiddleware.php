<?php

namespace Huacha\Permissions;

use Closure;
use Exception;
use Huacha\Permissions\Models\Action;
use Huacha\Permissions\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class PermissionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try{
            

            if(env("APP_DEBUG")) { return $next($request); } //test mode

            $config_permissions = config('permissions')['messages'];
            

            $route_name = Route::currentRouteName(); //get the current route

            if(is_null($route_name)){
                throw new Exception($config_permissions['invalid_route']);
            }

            $names = explode(".",$route_name);
            if(count($names) == 1){
                $search_accion = $names[0];
                $search_module = $names[0];
                
            }else if(count($names) >= 2){
                $search_accion = explode(':permission',$names[count($names)-1])[0];  //get name action
                $search_module = $names[count($names)-2]; //get name module
            }else {
                throw new Exception($config_permissions['invalid_route']);
            }

            /** Search module  if exist */

            $module = Module::where('prefix',$search_module)
            ->first();

            if(is_null($module)){
                throw new Exception($config_permissions['module_not_found']);
            }

            // dd($module,$search_accion);

            $accion = Action::where('module_id',$module->id)
            ->where('name',$search_accion)
            ->first();


            if(is_null($accion)){ //valido si existe la accion
                throw new Exception('action_not_found');
            }

            $role_id = Auth::user()->role->id;

            $access = Action::whereHas('roles',function($roleModel) use ($role_id,$accion) {
                $roleModel->where('role_id',$role_id)
                ->where('action_id',$accion->id);
            })
            ->where('module_id',$module->id)
            ->first();

            if(is_null($access)){
                throw new Exception($config_permissions['permissions_denied']);
            }

            return $next($request);
        }catch(Exception $e){

            if($request->ajax()){
                return response()->json([
                    'status'=>false,
                    'msg'=> $config_permissions['permissions_denied']
                ]);
            }

            abort(403);
            
        }




        
    }
}
