<?php

namespace Lamplighter\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionRole extends Model
{
    protected $guarded = [];
    protected $table = 'permissions_roles';
    //
    public function modules() : BelongsToMany{
        return $this->belongsToMany(PermissionModule::class,'permissions_roles_modules','role_id','module_id');
    }

    public function actions() : BelongsToMany{
        return $this->belongsToMany(PermissionAction::class,'permissions_roles_actions','role_id','action_id');
    }

    public function groups() : BelongsToMany{
        return $this->belongsToMany(PermissionGroup::class,'permissions_roles_groups','role_id','group_id');
    }
}
