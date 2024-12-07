<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'brand_name',
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
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_items')->withPivot('is_like');
    }
}
