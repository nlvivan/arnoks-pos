<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $searchable = [
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
