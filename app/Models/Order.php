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
         'quantity',
         'status',
         'payment',
         'user_id'
     ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pharmaceuticals()
    {
        return $this->belongsToMany(Pharmaceutical::class)->withPivot('quantity');
    }
    // public function pharmaceuticals(): BelongsToMany
    // {
    //     return $this->belongsToMany(Pharmaceutical::class);
    // }

    // public function details() {
    //     return $this->hasMany('l');
    // }
}
