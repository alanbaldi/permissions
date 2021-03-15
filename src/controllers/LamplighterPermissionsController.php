<?php

namespace Lamplighter\Permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lamplighter\Permissions\Models\Action;
use Lamplighter\Permissions\Models\Module;

class LamplighterPermissionsController extends Controller
{
    //


    public function get_modules(){
        $modules = Module::whereRaw('module_id = id')
        ->get(); //get all parent modules
        foreach($modules as $module){
            $module->actions = Action::where('module_id',$module->id)
            ->get();
            $module->children = $this->modules($module);
        }
        return $modules;
    }


    private function modules(Module $module){

        $modules = $module->modules()
        ->where('id','!=',$module->id)
        ->get();

        for($i=0;$i < count($modules);$i++){
            $modules[$i]->actions = Action::where('module_id',$modules[$i]->id)
            ->get();

            $modules[$i]->modules = $this->modules($modules[$i]);
        }
        return $modules;
    }
}
