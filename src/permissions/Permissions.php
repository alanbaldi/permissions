<?php 

namespace Lamplighter\Permissions\Perms;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Lamplighter\Permissions\Models\PermissionAction;
use Lamplighter\Permissions\Models\PermissionGroup;
use Lamplighter\Permissions\Models\PermissionModule;
use Lamplighter\Permissions\Models\PermissionRole;
use Symfony\Component\Console\Output\ConsoleOutput;

class Permissions{

    public static function make($role,$create){
        $routes = Route::getRoutes();

        foreach($routes as $route){
            $name_route = $route->getName();

            if(self::checkIsPermssion($name_route)){
                $names = explode(':permission',$name_route);
                unset($names[count($names)-1]);
                $array_names = explode('.',$names[0]);

                $action = PermissionAction::create([
                    'full_name' => $names[0]
                ]);
                self::generate($array_names,$action);
            }
        }
        //some modules are not related, so i relate them to themselves
        if(!is_null($role)) self::assignPermissions($role,$create);
        self::moduleRelashions();
    }

    public static function checkIsPermssion($name) : bool{
        if(is_null($name) || empty($name)){ return false; }
        return !empty(stristr($name,':permission'));
    }


    public static function createGroups(){
        DB::statement("SET foreign_key_checks=0");
        PermissionGroup::truncate();
        DB::table('permissions_roles_groups')->truncate();
        DB::table('permissions_groups_actions')->truncate();
        DB::table('permissions_roles_modules')->truncate();
        DB::statement("SET foreign_key_checks=1");
        $config = config('permissions');

        try{
            DB::beginTransaction();
            if(array_key_exists('groups',$config)){
                $groups = $config['groups'];
                $keys = array_keys($groups);
                foreach($keys as $key){

                    $mkeys = array_keys($groups[$key]);

                    $role = PermissionRole::where('description',$key)->first();
                    
                    if(is_null($role)){
                        throw new Exception('Don\'t exist role');
                    }

                    foreach($mkeys as $mkey){
                        $module = PermissionModule::where('name',$mkey)->first();
                        if(is_null($module)){
                            throw new Exception('Don\'t exist module');
                        }

                        $role->modules()->attach($module);
                        foreach($groups[$key][$mkey] as $g){
                            $group = PermissionGroup::create([
                                'name' => $g['name'],
                                'description' => $g['description'],
                                'module_id' => $module->id
                            ]);
                            if(array_key_exists('actions',$g)){
                                foreach($g['actions'] as $action){
                                    $action = PermissionAction::where('full_name',$action)->first();
                                    if(is_null($action)){ continue; }
                                    $group->actions()->attach($action);
                                    $role->actions()->attach($action);
                                }    
                            }

                            $role->groups()->attach($group);
        
                        }
                    }
    
                }
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        
    }

    public static function assignPermissions($nameRole,$create){

        $role = PermissionRole::where('description',$nameRole)->first();

        if(is_null($role)){
            if ($create){
                $role = PermissionRole::create([
                    'description' => $nameRole
                ]);
            }
        }
    
        $modules = PermissionModule::all();
        $actions = PermissionAction::all();
        $groups = PermissionGroup::all();
        $role->modules()->attach($modules);
        $role->actions()->attach($actions);
        $role->groups()->attach($groups);

    }


    private static function generate(array $names,PermissionAction $action){
        $total_names = count($names);

        if($total_names == 1){
            $module = PermissionModule::where('prefix',$names[0])->first() ?: PermissionModule::create([
                'name' => $names[0],
                'prefix' => $names[0],
                'description' => $names[0]
            ]);

            $action->update([
                'name' => $names[0],
                'prefix' => $names[0],
                'description' => $names[0],
                'module_id' => $module->id
            ]);

            
        }else {

            $module = null;
            $limit = 0;
            foreach($names as $name){
                if($limit === ($total_names -1)){
                    $action->update([
                        'name' => $name,
                        'prefix' => $name,
                        'description' => $name,
                        'module_id' => (!is_null($module)) ? $module->id : null
                    ]);
                }else {
                    $m = PermissionModule::where('prefix',$name)->first();

                    $module = $m ?: PermissionModule::create([
                        'name' => $name,
                        'prefix' => $name,
                        'description' => $name,
                        'module_id' => (!is_null($module)) ? $module->id : null 
                    ]);
                }
                $limit++;

            }
        }
    }
    public static function truncate(){
        DB::statement("SET foreign_key_checks=0");
        PermissionModule::truncate();
        PermissionAction::truncate();
        PermissionGroup::truncate();
        DB::table('permissions_roles_actions')->truncate();
        DB::table('permissions_roles_modules')->truncate();
        DB::statement("SET foreign_key_checks=1");
    }

    private static function moduleRelashions(){
        $modules = PermissionModule::where([
            'module_id' => null
        ])->get();

        foreach($modules as $module){
            $module->update([
                'module_id' => $module->id
            ]);
        }
    }

    public static function parseRoute($nameRoute) : string{
        $route = explode(':permissions',$nameRoute);
        unset($route[':permissions']);
        return implode('',$route);
    }
}