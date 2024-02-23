<?php

namespace App\Enums;

use Rexlabs\Enum\Enum;

/**
 * The UserRole enum.
 *
 * @method static self ADMIN()
 * @method static self TEACHER()
 * @method static self STUDENT()
 */
class UserRole extends Enum
{
    const ADMIN = 1;
    const TEACHER = 2;
    const STUDENT = 3;
}
