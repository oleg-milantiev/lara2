<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manufacturer extends Model
{
    use HasFactory;

    protected $table = 'manufacturer';
    protected $primaryKey = 'manufacturer_id';
    public $timestamps = false;

    protected $fillable = ['manufacturer_name'];

    public function products()
    {
        return $this->hasMany(Product::class, 'manufacturer_id', 'manufacturer_id');
    }
}
