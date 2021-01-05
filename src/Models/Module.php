<?php

namespace Huacha\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    //
    protected $guarded = [];
    public function actions() : HasMany{
        return $this->hasMany(Action::class);
    }
}
