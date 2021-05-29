<?php

namespace Lamplighter\Permissions;

use Closure;
use Exception;
use Lamplighter\Permissions\Models\Action;
use Lamplighter\Permissions\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Lamplighter\Permissions\Models\PermissionAction;
use Lamplighter\Permissions\Models\PermissionModule;

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

        $config_permissions = config('permissions')['messages'];

        try{

            // if(env("APP_DEBUG")) { return $next($request); } //test mode

            $route_name = Route::currentRouteName(); //get the current route

            if(!Permissions::checkIsPermssion($route_name)) return $next($request);

            $route = Permissions::parseRoute($route_name);

            $user = $request->user();

            

            if(is_null($user)) throw new Exception($config_permissions['invalid_route']);
            $checkPermission = $user->hasPerm($route);

            if(!$checkPermission) throw new Exception($config_permissions['permissions_denied']);
            return $next($request);

            // $route_name = Route::currentRouteName(); //get the current route

            // dd($route_name);

            // if(is_null($route_name)){
            //     throw new Exception($config_permissions['invalid_route']);
            // }

            // $names = explode(".",$route_name);
            // if(count($names) == 1){
            //     $search_accion = $names[0];
            //     $search_module = $names[0];
                
            // }else if(count($names) >= 2){
            //     $search_accion = explode(':permission',$names[count($names)-1])[0];  //get name action
            //     $search_module = $names[count($names)-2]; //get name module
            // }else {
            //     throw new Exception($config_permissions['invalid_route']);
            // }

            // /** Search module  if exist */

            // $module = PermissionModule::where('prefix',$search_module)
            // ->first();

            // if(is_null($module)){
            //     throw new Exception($config_permissions['module_not_found']);
            // }

            // // dd($module,$search_accion);

            // $accion = PermissionAction::where('module_id',$module->id)
            // ->where('name',$search_accion)
            // ->first();


            // if(is_null($accion)){ //valido si existe la accion
            //     throw new Exception('action_not_found');
            // }

            // $role_id = Auth::user()->role->id;

            // $access = PermissionAction::whereHas('roles',function($roleModel) use ($role_id,$accion) {
            //     $roleModel->where('role_id',$role_id)
            //     ->where('action_id',$accion->id);
            // })
            // ->where('module_id',$module->id)
            // ->first();

            // if(is_null($access)){
            //     throw new Exception($config_permissions['permissions_denied']);
            // }

            // return $next($request);
        }catch(Exception $e){
            if($request->ajax()){
                return response()->json([
                    'status'=>false,
                    'msg'=> $config_permissions['permissions_denied']
                ],403);
            }

            abort(403);
            
        }
    }
}
