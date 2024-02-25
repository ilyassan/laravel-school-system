<?php

namespace App\Models;

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

    // The absent student
    public function student(){
        return $this->belongsTo(User::class, 'student_id');
    }

    // The teacher who mark it
    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id');
    }

}
