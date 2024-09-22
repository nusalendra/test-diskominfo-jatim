<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['name', 'price', 'stock'];
    protected $guarded = [];

    public function order() {
        return $this->hasMany(Order::class, 'product_id');
    }
}
