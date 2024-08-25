<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'grade',
    ];

    public const PRIMARY_KEY = 'id';

    public const GRADE_COLUMN = 'grade';

    public const TEACHER_COLUMN = 'teacher_id';

    public const STUDENT_COLUMN = 'student_id';

    public function getCreatedAtFormated(): string
    {
        return $this->getAttributeValue(self::CREATED_AT)->format('m/d/Y');
    }
    public function getCreatedAtDetailsFormated(): string
    {
        return $this->getAttributeValue(self::CREATED_AT)->format('m/d/Y \A\t H:i:s');
    }
    public function getUpdatedAtDetailsFormated(): ?string
    {
        return $this->getAttributeValue(self::UPDATED_AT)?->format('m/d/Y \A\t H:i:s');
    }


    // Relations

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
