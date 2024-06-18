<?php

namespace App\Http\Controllers;

use App\Models\User;

class BaseController extends Controller
{
    public function getAuthUser(): User
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user;
    }
}