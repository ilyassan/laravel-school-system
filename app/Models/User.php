<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guarded = [];


    /* ### Scope ### */
    
    // Get admins only
    public function scopeAdmins($query)
    {
        return $query->where('role_id', UserRole::ADMIN);
    }

    // Get teachers only
    public function scopeTeachers($query)
    {
        return $query->where('role_id', UserRole::TEACHER);
    }

    // Get students only
    public function scopeStudents($query)
    {
        return $query->where('role_id', UserRole::STUDENT);
    }


    /* ### Student Relations ### */

    // Relationship: Student belongs to a class
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // Relationship: Student has many absences
    public function studentAbsences()
    {
        return $this->hasMany(Absence::class, 'student_id');
    }
    

    /* ### Teacher Relations ### */

    // Relationship: Teacher has one subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Relationship: Teacher has many classes
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_teacher', 'teacher_id', 'class_id');
    }

    // Relationship: Teacher has marked many absences
    public function markedAbsences()
    {
        return $this->hasMany(Absence::class, 'teacher_id');
    }
    

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}
