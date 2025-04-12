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
        return $this->belongsToMany(Batch::class, 'batch_course_user')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'batch_course_user')
            ->withPivot('batch_id')
            ->withTimestamps();
    }
}
