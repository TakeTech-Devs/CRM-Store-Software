<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'store';

    protected $fillable = [
        'id',
        'name',
        'store_address',
        'dl_number',
        'helpline_number',
        'store_mail',
        'store_start_date',
        'store_meta_id',
        'store_pass_key',
        'store_status',
        'store_verify_status',
    ];
}
