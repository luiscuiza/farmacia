<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'stock', 'quantity', 'expiration', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}