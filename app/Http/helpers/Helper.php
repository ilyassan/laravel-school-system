<?php

namespace App\Helpers;

class Helper
{

    public static function profile_images_folder(): string
    {
        return 'profile_images/';
    }
    public static function profile_images_path(): string
    {
        return "public/" . self::profile_images_folder() . "/";
    }
}
