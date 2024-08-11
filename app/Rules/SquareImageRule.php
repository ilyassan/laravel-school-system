<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SquareImageRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $manager = new ImageManager(new Driver());

        try {
            $image = $manager->read($value->path());
            $width = $image->width();
            $height = $image->height();

            if ($width !== $height) {
                $fail('The :attribute must be a square image (width and height must be equal).');
            }
        } catch (\Exception $e) {
            $fail('The :attribute must be a valid image file.');
        }
    }
}