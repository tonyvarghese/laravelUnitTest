<?php

namespace App;

//use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
//    use SoftDeletes;
    
    protected $fillable = [
        'name', 'price', 'units', 'description', 'image'
    ];
}
