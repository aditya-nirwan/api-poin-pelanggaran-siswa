<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'category_id',
        'tanggal',
        'poin',
        'catatan'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function category()
    {
        return $this->belongsTo(ViolationCategory::class, 'category_id');
    }
}
