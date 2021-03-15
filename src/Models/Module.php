<?php

namespace Lamplighter\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionModule extends Model
{
    //
    protected $table = 'permissions_modules';
    protected $guarded = [];
    public function actions() : HasMany{
        return $this->hasMany(PermissionAction::class,'id','module_id');
    }

    public function modules() : HasMany{
        return $this->hasMany(PermissionModule::class,'id','module_id');
    }
}
