<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmaceutical extends Model
{
    use HasFactory;
    protected $table = 'pharmaceuticals';

    protected $fillable = [
        'scientific_name',
        'commercial_name',
        'calssification',
        'manufacture_company',
        'quantity_available',
        'expire_date',
        'price',
    ];  
   
}
