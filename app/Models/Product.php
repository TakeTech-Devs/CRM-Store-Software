<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product';

    protected $fillable = [
        'id',
        'product_name',
        'brand_id',
        'category_id',
        'sub_category_id',
        'hsn_code',
        'gst',
        'status',
    ];
}
