<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    protected $fillable = [
        'min_poin',
        'max_poin',
        'sanksi',
    ];

    // relasi
    public function guidances()
    {
        return $this->hasMany(Guidance::class);
    }
}
