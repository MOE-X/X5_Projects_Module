<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'project_topic_id',
        'start_date',
        'end_date',
        'production_link',
        'web_github_link',
        'mobile_github_link',
        'logo',
        'is_open',
        'is_completed',
        'user_id'
    ];

    public function topic()
    {
        return $this->belongsTo(ProjectTopic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users() 
    {
        return $this->belongsToMany(User::class, 'project_role_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'project_role_user')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}