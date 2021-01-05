<?php

namespace Huacha\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $guarded = [];
    //
    public function modules() : BelongsToMany{
        return $this->belongsToMany(Module::class,'roles_modules');
    }

    public function actions() : BelongsToMany{
        return $this->belongsToMany(Action::class,'roles_actions');
    }

    public function groups() : BelongsToMany{
        return $this->belongsToMany(Group::class);
    }
}
