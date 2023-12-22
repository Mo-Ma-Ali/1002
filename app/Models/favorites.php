<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favorites extends Model
{
    use HasFactory;



    public function favoritedBy()
{
    return $this->belongsToMany(User::class, 'favorites');
}
}
