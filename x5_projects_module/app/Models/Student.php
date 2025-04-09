<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'github_link',
        'linkedin_link',
        'is_completed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'project_role_student')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'project_role_student')
            ->withPivot('project_id')
            ->withTimestamps();
    }
}
