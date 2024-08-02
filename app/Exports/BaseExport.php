<?php

namespace App\Exports;


class BaseExport
{
    static public $tempFolder = 'temps';
    static public $downloadFolder = 'downloads';

    static public function getTempFolderPath()
    {
        return storage_path('app/public/' . self::$tempFolder . '/');
    }
    static public function getDownloadFolderPath()
    {
        return storage_path('app/public/' . self::$downloadFolder . '/');
    }

    static public function getIdFromFileName($fileName)
    {
        return explode('-', $fileName)[0];
    }
}