<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;

    protected $table = "homeworks";

    public const TEACHER_COLUMN = "teacher_id"; 
    
    public const CLASS_COLUMN = "class_id"; 

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relationship: The subject of the teacher who create the homework
    public function subject()
    {
        return $this->hasOneThrough(
            Subject::class,
            User::class,
            'id',
            'id',
            'teacher_id',
            'subject_id'
        );
    }

    protected $casts = [
        'end_date' => 'datetime',
    ]; 
}
