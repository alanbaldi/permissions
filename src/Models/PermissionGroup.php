<?php

namespace Lamplighter\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionGroup extends Model
{

    protected $table = 'permissions_groups';
    //
    protected $guarded = [];

    public function actions() : BelongsToMany{
        return $this->belongsToMany(PermissionAction::class,'permissions_groups_actions','group_id','action_id');
    }



    
}
