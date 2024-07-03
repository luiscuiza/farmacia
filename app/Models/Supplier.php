<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

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