<?php 

namespace Huacha\Permissions;

use Illuminate\Support\Facades\Route;

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
                self::make($array_names,$action);
            }
        }

    }

}