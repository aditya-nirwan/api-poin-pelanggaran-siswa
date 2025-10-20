<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ViolationCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'kategori',
        'sub',
        'jenis_pelanggaran',
        'poin'
    ];

    // relasi
    public function violations()
    {
        return $this->hasMany(Violation::class, 'category_id');
    }
}
