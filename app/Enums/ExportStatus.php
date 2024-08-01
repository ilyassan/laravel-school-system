<?php

namespace App\Enums;

use Rexlabs\Enum\Enum;

/**
 * The ExportStatus enum.
 *
 * @method static self ADMIN()
 * @method static self TEACHER()
 * @method static self STUDENT()
 */
class ExportStatus extends Enum
{
    const IN_PROGRESS = 'in-progress';
    const CANCELLED = 'cancelled';
    const COMPLETED = 'completed';
}
