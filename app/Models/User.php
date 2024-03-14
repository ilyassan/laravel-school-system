<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Models\ClassTeacher;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use League\CommonMark\Environment\Environment;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guarded = [];

    public const ROLE_COLUMN = "role_id"; 

    public const NAME_COLUMN = "name"; 

    public const GENDER_COLUMN = "gender"; 
    
    public const GENDER_MALE = "M"; 

    public const GENDER_FEMALE = "F"; 

    /* ### Scope ### */
    
    // Get admins only
    public function scopeAdmins($query)
    {
        return $query->where(self::ROLE_COLUMN, UserRole::ADMIN);
    }

    // Get teachers only
    public function scopeTeachers($query)
    {
        return $query->where(self::ROLE_COLUMN, UserRole::TEACHER);
    }

    // Get students only
    public function scopeStudents($query)
    {
        return $query->where(self::ROLE_COLUMN, UserRole::STUDENT);
    }

    // Get students and teachers
    public function scopeStudentsAndTeachers($query)
    {
        return $query->whereIn(self::ROLE_COLUMN, [UserRole::STUDENT, UserRole::TEACHER]);
    }


    /* ### Student Relations ### */


    // Relationship: Student belongs to a class
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // Relationship: Student has many grades
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    // Relationship: Student has many absences
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'student_id');
    }

    public function studentTeachers()
    {
        return $this->class->teachers();
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
    public function markedGrades()
    {
        return $this->hasMany(Grade::class, 'teacher_id');
    }

    // Relationship: Teacher has marked many absences
    public function markedAbsences()
    {
        return $this->hasMany(Absence::class, 'teacher_id');
    }

    /* ### Commun Relations ### */

    public function reports()
    {
        return $this->hasMany(Report::class, 'user_id');
    }
    

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}
