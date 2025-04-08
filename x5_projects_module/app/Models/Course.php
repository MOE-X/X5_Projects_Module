<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name'
    ];

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_course_student')
            ->withPivot('student_id')
            ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'batch_course_student')
            ->withPivot('batch_id')
            ->withTimestamps();
    }
}
