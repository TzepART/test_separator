<?php
declare(strict_types=1);


namespace TestSeparator\Handler;


class ServicesSeparateTestsFactory
{

    public const DIRECTORY_LEVEL = 'directory_level';
    public const FILE_LEVEL      = 'file_level';

    public static function makeLevelDeepService(string $serviceName): LevelDeepInterface
    {
        switch ($serviceName) {
            case self::DIRECTORY_LEVEL:
                $service = new DirectoryDeepService();
                break;
            case self::FILE_LEVEL:
                $service = new FileSystemHelper();
                break;
            default:
                $service = new DirectoryDeepService();
                break;
        }

        return $service;
    }

    public static function makeFileSystemHelper(): FileSystemHelper
    {
        return new FileSystemHelper();
    }
}
