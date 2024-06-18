<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    const TABLE = 'subjects';
    const PRIMARY_KEY_COLUMN_NAME = "id";
    const NAME_COLUMN = "name";

    public function teachers()
    {
        return $this->hasMany(User::class, 'subject_id');
    }
}
