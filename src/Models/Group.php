<?php

namespace Huacha\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    //
    protected $guarded = [];

    public function actions() : BelongsToMany{
        return $this->belongsToMany(Action::class,'groups_actions');
    }
}
