<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = [
        'tahun_awal',
        'tahun_akhir',
        'semester_ganjil_start',
        'semester_ganjil_end',
        'semester_genap_start',
        'semester_genap_end',
        'is_active',
    ];

    protected $casts = [
        'semester_ganjil_start' => 'date',
        'semester_ganjil_end' => 'date',
        'semester_genap_start' => 'date',
        'semester_genap_end' => 'date',
        'is_active' => 'boolean',
    ];
}
