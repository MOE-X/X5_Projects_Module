<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'task_type_id',
        'video_link',
        'project_id',
        'task_status_id',
        'due_date',
        'result',
    ];

    public function project()
    {
        return $this->belongsTo(Course::class);
    }

    public function type()
    {
        return $this->belongsTo(TaskType::class);
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class);
    }
}
