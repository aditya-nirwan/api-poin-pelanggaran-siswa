<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Student extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'kelas',
        'no_hp',
        'total_poin',
    ];

    protected function foto(): Attribute
    {
        return Attribute::make(
            get: fn($foto) => $foto ? url('/storage/users/' . $foto) : null,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function guidances()
    {
        return $this->hasMany(Guidance::class);
    }
}
