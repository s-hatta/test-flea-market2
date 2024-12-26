<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected $fillable = [
        'condition',
    ];
    
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
