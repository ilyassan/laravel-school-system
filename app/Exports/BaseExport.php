<?php

namespace App\Exports;


class BaseExport
{
    static public $tempFolder = 'temps';
    static public $downloadFolder = 'downloads';

    static public function ensureTempsFolderExists()
    {
        $tempsFolderPath = self::getTempFolderPath();

        // Check if the temp folder path exists
        if (!file_exists($tempsFolderPath)) {
            // Create the folder and any necessary parent directories
            mkdir($tempsFolderPath);
        }
    }

    static public function ensureDownloadsFolderExists()
    {
        $downloadsFolderPath = self::getDownloadFolderPath();

        // Check if the temp folder path exists
        if (!file_exists($downloadsFolderPath)) {
            // Create the folder and any necessary parent directories
            mkdir($downloadsFolderPath);
        }
    }

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