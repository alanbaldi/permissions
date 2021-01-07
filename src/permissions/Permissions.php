<?php 

namespace Huacha\Permissions;

use Huacha\Permissions\Models\Action;
use Huacha\Permissions\Models\Group;
use Huacha\Permissions\Models\Module;
use Huacha\Permissions\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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

                $action = Action::create([
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

                $group = Group::create([
                    'name' => $g['name'],
                    'description' => $g['description']
                ]);

                if(array_key_exists('actions',$g)){

                    foreach($g['actions'] as $action){
                        $action = Action::where('full_name',$action)->first();
                        if(is_null($action)){ continue; }
                        $group->actions()->attach($action);
                    }
                    
                }

            }
        }
    }

    private static function assignPermissions(){
        $root = Role::where('description','root')->first() ?: Role::create([
            'description' => 'root'
        ]);

        $modules = Module::all();
        $actions = Action::all();
        $groups = Group::all();
        $root->modules()->attach($modules);
        $root->actions()->attach($actions);
        $root->groups()->attach($groups);

    }


    private static function generate(array $names,Action $action){
        $total_names = count($names);

        if($total_names == 1){
            $module = Module::where('prefix',$names[0])->first() ?: Module::create([
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
                    $m = Module::where('prefix',$name)->first();

                    $module = $m ?: Module::create([
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
        Module::truncate();
        Action::truncate();
        Group::truncate();
        DB::table('roles_actions')->truncate();
        DB::table('roles_groups')->truncate();
        DB::table('groups_actions')->truncate();
        DB::statement("SET foreign_key_checks=1");
    }

    private static function moduleRelashions(){
        $modules = Module::where([
            'module_id' => null
        ])->get();

        foreach($modules as $module){
            $module->update([
                'module_id' => $module->id
            ]);
        }
    }
}