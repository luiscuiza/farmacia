<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'barcode', 'description', 'laboratory_id'];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_product');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }
}