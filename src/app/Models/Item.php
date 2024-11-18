<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'condition_id',
        'price',
        'stock',
        'detail',
        'img_url',
    ];
    
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_items');
    }
    
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
    
    public function order()
    {
        return $this->hasOne(Orders::class);
    }
    
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
