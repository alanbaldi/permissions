<?php 

namespace Lamplighter\Permissions;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Lamplighter\Permissions\Models\PermissionAction;
use Lamplighter\Permissions\Models\PermissionGroup;
use Lamplighter\Permissions\Models\PermissionModule;
use Lamplighter\Permissions\Models\PermissionRole;
use Symfony\Component\Console\Output\ConsoleOutput;

class Permissions{



    public static function make(){
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

        self::createGroups();
        self::assignPermissions();

        //some modules are not related, so i relate them to themselves
        self::moduleRelashions();

    }

    private static function checkIsPermssion($name) : bool{
        if(is_null($name) || empty($name)){ return false; }
        return !empty(stristr($name,':permission'));
    }


    private static function createGroups(){

        $config = config('permissions');

        if(array_key_exists('groups',$config)){
            $groups = $config['groups'];

            foreach($groups as $g){

                $group = PermissionGroup::create([
                    'name' => $g['name'],
                    'description' => $g['description']
                ]);

                if(array_key_exists('actions',$g)){

                    foreach($g['actions'] as $action){
                        $action = PermissionAction::where('full_name',$action)->first();
                        if(is_null($action)){ continue; }
                        $group->actions()->attach($action);
                    }
                    
                }

            }
        }
    }

    private static function assignPermissions(){
        $root = PermissionRole::where('description','root')->first() ?: PermissionRole::create([
            'description' => 'root'
        ]);

        $modules = PermissionModule::all();
        $actions = PermissionAction::all();
        $groups = PermissionGroup::all();
        $root->modules()->attach($modules);
        $root->actions()->attach($actions);
        $root->groups()->attach($groups);

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
        DB::table('permissions_roles_groups')->truncate();
        DB::table('permissions_groups_actions')->truncate();
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
}