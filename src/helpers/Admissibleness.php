<?php


namespace Lamplighter\Permissions;

use Lamplighter\Permissions\Models\PermissionAction;
use Lamplighter\Permissions\Models\PermissionModule;

trait Admissibleness{

    /**
     * @return BelongsTo
     */

    public function role() : \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(\Lamplighter\Permissions\Models\PermissionRole::class);
    }


    /**
     * 
     * @param string|null
     * @return Collection
     */
    public function getPerms($moduleName = null){

        if(is_null($moduleName)) return $this->getAllPerms();

        $module = PermissionModule::where('prefix',$moduleName)->first();

        $hasPerm = $this->hasPermission($module); 

        if(!$hasPerm) return collect([]);

        return $this->getActions($module);
    }

    /**
     * 
     * @param \Lamplighter\Permissions\Models\PermissionModule|null
     * @return Illuminate\Support\Collection
     */
    protected function getActions(?\Lamplighter\Permissions\Models\PermissionModule $module) : \Illuminate\Support\Collection{
        $collecion = collect([]);
        if(is_null($module)) $collecion;
        
        $childrenModule = PermissionModule::where('module_id',$module->id)
        ->where('id','!=', $module->id)
        ->get();

        $moduleActions = $module->actions;
        
        $collecion = $collecion->merge($moduleActions);
        foreach($childrenModule as $childModule){
            $actions = $childModule->actions;
            $childActions = $this->getActions($childModule);
            $collecion = $collecion->merge(...$actions,...$childActions);
        }
        return $collecion;
    }

    /**
     * 
     * @param string
     * @return bool
     */

    public function hasPerm(string $actionName) : bool{
        if(!method_exists($this,'role')) return false;
        $action = PermissionAction::where('full_name',$actionName)->first();

        if(is_null($action)) return false;

        $hasPerm = $this->role->actions()
        ->where('action_id',$action->id)
        ->first();
        
        return !is_null($hasPerm);
    }

    private function hasPermission(PermissionModule $module){
        if(!method_exists($this,'role')) return false;

        $hasPerm = $this->role->modules()->where([
            'module_id' => $module->id
        ])
        ->first();

        return !is_null($hasPerm);
    }


    /**
     * 
     * @param null
     * @return Illuminate\Support\Collection
     */


    private function getAllPerms() : \Illuminate\Support\Collection{
        if(!method_exists($this,'role')) return collect([]);

        // $collecion = collect([]);

        // $modules = $this->role->modules;
        // foreach($modules as $module){
        //     $actions = $this->getActions($module);
        //     $collecion = $collecion->merge($actions);
        // }
        return $this->role->actions;
    }

    /**
     * 
     * @param null
     * @return Illuminate\Support\Collection
     */
    public function getModules() :  \Illuminate\Support\Collection{
        if(!method_exists($this,'role')) return collect([]);
        return $this->role->modules;
    }

}