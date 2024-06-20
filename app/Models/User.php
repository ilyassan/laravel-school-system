<?php

namespace App\Models;

use App\Enums\UserRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guarded = [];

    public const TABLE = "users";
    public const PRIMARY_KEY_COLUMN_NAME = "id";
    public const ROLE_COLUMN = "role_id";

    public const FIRST_NAME_COLUMN = "first_name";

    public const LAST_NAME_COLUMN = "last_name";

    public const CLASS_COLUMN = "class_id";

    public const GENDER_COLUMN = "gender";

    public const GENDER_MALE = "M";

    public const GENDER_FEMALE = "F";


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];


    public function getRoleId()
    {
        return $this->getAttributeValue(self::ROLE_COLUMN);
    }

    /* ### Check Role ### */

    public function isAdmin()
    {
        return $this->getRoleId() == UserRole::ADMIN;
    }

    public function isTeacher()
    {
        return $this->getRoleId() == UserRole::TEACHER;
    }

    public function isStudent()
    {
        return $this->getRoleId() == UserRole::STUDENT;
    }

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

    // Search user by fullname
    public function scopeWhereFullNameLike($query, $search)
    {
        return $query->where(DB::raw("CONCAT(" . self::TABLE . "." . self::FIRST_NAME_COLUMN . ", ' ', " . self::TABLE . "." . self::LAST_NAME_COLUMN . ")"), 'LIKE', '%' . $search . '%');
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

    /* ### Attributes ### */

    public function getFullnameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
