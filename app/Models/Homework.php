<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;

    protected $table = "homeworks";

    public const TEACHER_COLUMN = "teacher_id"; 

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    protected $casts = [
        'end_date' => 'datetime',
    ]; 
}
