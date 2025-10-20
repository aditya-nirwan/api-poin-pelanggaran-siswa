<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Guidance extends Model
{
    /** @use HasFactory<\Database\Factories\GuidanceFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'sanction_id',
        'tanggal',
        'catatan',
        'status',
    ];

    protected function spFile(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? url('/storage/sp/' . $value) : null,
        );
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function sanction()
    {
        return $this->belongsTo(Sanction::class);
    }
}
