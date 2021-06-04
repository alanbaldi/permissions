<?php

namespace Lamplighter\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionAction extends Model
{
    //
    protected $table = 'permissions_actions';
    protected $guarded = [];

    public function module() : BelongsTo{
        return $this->belongsTo(PermissionModule::class,'module_id','id');
    }

    public function group() : BelongsTo{
        return $this->belongsTo(PermissionGroup::class);
    }

    public function roles() : BelongsToMany{
        return $this->belongsToMany(PermissionRole::class,'permissions_roles_actions','action_id','role_id');
    }


}
