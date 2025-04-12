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
        return $this->belongsToMany(Course::class, 'batch_course_user')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    public function users(){
        return $this->belongsToMany(User::class, 'batch_course_user')
            ->withPivot('course_id')
            ->withTimestamps();
    }
}
