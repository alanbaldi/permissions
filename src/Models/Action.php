<?php

namespace Huacha\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    //
    protected $guarded = [];

    public function module() : BelongsTo{
        return $this->belongsTo(Module::class);
    }

    public function group() : BelongsTo{
        return $this->belongsTo(Group::class);
    }
}
