<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    public function students() {
        return $this->belongsToMany(Student::class, 'project_role_student')
            ->withPivot('project_id')
            ->withTimestamps();
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'project_role_student')
            ->withPivot('student_id')
            ->withTimestamps();
    }
}
