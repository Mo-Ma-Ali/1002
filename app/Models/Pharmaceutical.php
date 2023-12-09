<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function orders() : BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }
}
