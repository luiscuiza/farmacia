<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'laboratory_id'];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'supplier_product');
    }
}