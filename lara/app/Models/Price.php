<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    protected $table = 'price';
    protected $primaryKey = 'price_id';
    public $timestamps = false;

    protected $fillable = ['product_id', 'price', 'price_date'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
