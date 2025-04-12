<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'project_role_user')
            ->withPivot('project_id')
            ->withTimestamps();
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'project_role_user')
            ->withPivot('user_id')
            ->withTimestamps();
    }
}
