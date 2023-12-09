<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
         'pharmaceutical_id',
         'quantity'
     ];

    // public function pharmaceuticals(): BelongsToMany
    // {
    //     return $this->belongsToMany(Pharmaceutical::class);
    // }

    // public function details() {
    //     return $this->hasMany('l');
    // }
}
