<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'dob',
        'gender_id',
        'user_role_id',
        'github_link',
        'linkedin_link',
        'is_completed',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function gender(){
        return $this->belongsTo(Gender::class);
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_course_user')
            ->withPivot('course_id')
            ->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'batch_course_user')
            ->withPivot('batch_id')
            ->withTimestamps();
    }

    public function projects() 
    {
        return $this->belongsToMany(Project::class, 'project_role_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'project_role_user')
            ->withPivot('project_id')
            ->withTimestamps();
    }


}
