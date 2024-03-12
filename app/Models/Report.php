<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
    ];

    public const DESCRIPTION_COLUMN = 'description';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Get teachers reports only
    public function scopeTeacherReports($query)
    {
        return $query->whereHas('user', function (Builder $query) {
            $query->where('role_id', UserRole::TEACHER);
        });
    }

    // Get students reports only
    public function scopeStudentReports($query)
    {
        return $query->whereHas('user', function (Builder $query) {
            $query->where('role_id', UserRole::STUDENT);
        });
    }
}
