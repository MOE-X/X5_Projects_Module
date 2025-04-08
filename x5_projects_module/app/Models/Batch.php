<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'batch_course_student')
            ->withPivot('student_id')
            ->withTimestamps();
    }

    public function students(){
        return $this->belongsToMany(Student::class, 'batch_course_student')
            ->withPivot('course_id')
            ->withTimestamps();
    }
}
