<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'from',
        'to',
    ];

    public function student(){
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id');
    }

}
