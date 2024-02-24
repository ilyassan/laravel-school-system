<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    public function students(){
        return $this->hasMany(User::class, 'class_id')->where('role_id', UserRole::STUDENT);
    }

    public function teachers(){
        return $this->belongsToMany(User::class, 'class_teacher', 'class_id', 'teacher_id');
    }

}
