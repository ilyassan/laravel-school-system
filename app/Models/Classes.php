<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    public const NAME_COLUMN = 'name';

    public const AVG_GRADES = 'avgGrades';

    public function students()
    {
        return $this->hasMany(User::class, 'class_id')->where('role_id', UserRole::STUDENT);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'class_teacher', 'class_id', 'teacher_id');
    }

    public function grades()
    {
        return $this->hasManyThrough(Grade::class, User::class, 'class_id', 'student_id');
    }

    public function absences()
    {
        return $this->hasManyThrough(Absence::class, User::class, 'class_id', 'student_id');
    }

    public function homeworks()
    {
        return $this->hasMany(Homework::class, 'class_id');
    }

    public function scopeWithAvgGrades($query, $date = 0)
    {
        return $query->withAvg(["grades as ". self::AVG_GRADES => function (Builder $query) use ($date) {
            $query->where('grades.'. Grade::CREATED_AT, '>=', $date);
        }], Grade::GRADE_COLUMN);
    }
}
