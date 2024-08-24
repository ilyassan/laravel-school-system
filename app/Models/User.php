<?php

namespace App\Models;

use App\Enums\UserGender;
use App\Enums\UserRole;
use App\Helpers\Helper;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public const TABLE = "users";

    protected $fillable = [
        self::PRIMARY_KEY_COLUMN_NAME,
        self::ROLE_COLUMN,
        self::FIRST_NAME_COLUMN,
        self::LAST_NAME_COLUMN,
        self::EMAIL_COLUMN,
        self::PHONE_COLUMN,
        self::PASSWORD_COLUMN,
        self::BIO_COLUMN,
        self::SALARY_COLUMN,
        self::GENDER_COLUMN,

        self::CLASS_COLUMN,
        self::SUBJECT_COLUMN,
        self::IMAGE_PATH_COLUMN,
    ];

    public const PRIMARY_KEY_COLUMN_NAME = "id";
    public const ROLE_COLUMN = "role_id";
    public const FIRST_NAME_COLUMN = "first_name";
    public const LAST_NAME_COLUMN = "last_name";
    public const EMAIL_COLUMN = "email";
    public const PHONE_COLUMN = "phone";
    public const PASSWORD_COLUMN = "password";
    public const BIO_COLUMN = "bio";
    public const SALARY_COLUMN = "salary";
    public const GENDER_COLUMN = "gender";
    public const CLASS_COLUMN = "class_id";
    public const SUBJECT_COLUMN = "subject_id";
    public const IMAGE_PATH_COLUMN = "image_path";


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];


    public function getKey(): string
    {
        return $this->getAttributeValue(self::PRIMARY_KEY_COLUMN_NAME);
    }
    public function getRoleId(): string
    {
        return $this->getAttributeValue(self::ROLE_COLUMN);
    }
    public function getRoleName(): ?string
    {
        $roles = [
            UserRole::ADMIN => 'Admin',
            UserRole::TEACHER => 'Teacher',
            UserRole::STUDENT => 'Student',
        ];

        return $roles[$this->getRoleId()] ?? null;
    }
    public function getFirstName(): string
    {
        return $this->getAttributeValue(self::FIRST_NAME_COLUMN);
    }
    public function getLastName(): string
    {
        return $this->getAttributeValue(self::LAST_NAME_COLUMN);
    }
    public function getFullName(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }
    public function getEmail(): string
    {
        return $this->getAttributeValue(self::EMAIL_COLUMN);
    }
    public function getPhone(): string
    {
        return $this->getAttributeValue(self::PHONE_COLUMN);
    }
    public function getSalary(): string
    {
        return $this->getAttributeValue(self::SALARY_COLUMN);
    }
    public function getBio(): string
    {
        return $this->getAttributeValue(self::BIO_COLUMN) ?? "Hi, I'm " . $this->getFullName() . " and I am a " . $this->getRoleName() . " at this school.";
    }
    public function getGender(): string
    {
        return $this->getAttributeValue(self::GENDER_COLUMN) == UserGender::GENDER_MALE ? 'Male' : 'Female';
    }
    public function getClassId(): string
    {
        return $this->getAttributeValue(self::CLASS_COLUMN);
    }
    public function getSubjectId(): string
    {
        return $this->getAttributeValue(self::SUBJECT_COLUMN);
    }
    public function getImagePath(): ?string
    {
        return $this->getAttributeValue(self::IMAGE_PATH_COLUMN);
    }
    public function getImage(): string
    {
        $image_path = $this->getImagePath();
        return $image_path ? Storage::url(Helper::profile_images_folder() . $image_path) : asset('assets/img/faces/1.webp');
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


    /* ### Student Relations And Methods ### */

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

    // Calculate the average grade for all student subjects.
    public function avgGradesOfSubjects()
    {
        return $this->grades()
            ->join('users', 'grades.teacher_id', '=', 'users.id')
            ->join('subjects', 'users.subject_id', '=', 'subjects.id')
            ->select('subjects.name as subject_name', DB::raw('AVG(grades.grade) as average_grade'))
            ->groupBy('subjects.name')
            ->pluck('average_grade', 'subject_name');
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
}
