<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $filable = [
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

    public function students()
    {
        return $this->belongsToMany(Student::class, 'project_student');
    }
}
