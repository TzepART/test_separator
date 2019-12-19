<?php
declare(strict_types=1);


namespace TestSeparator\Handler;


use TestSeparator\Strategy\DirectoryDeepStrategyService;
use TestSeparator\Strategy\LevelDeepStrategyInterface;

class ServicesSeparateTestsFactory
{

    public const DIRECTORY_LEVEL = 'directory_level';
    public const FILE_LEVEL      = 'file_level';
    public const TEST_LEVEL      = 'test_level';

    public static function makeLevelDeepService(string $serviceName): LevelDeepStrategyInterface
    {
        switch ($serviceName) {
            case self::DIRECTORY_LEVEL:
                $service = new DirectoryDeepStrategyService();
                break;
            case self::FILE_LEVEL:
                $service = new FileSystemHelper();
                break;
            case self::TEST_LEVEL:
                $service = new FileSystemHelper();
                break;
            default:
                $service = new DirectoryDeepStrategyService();
                break;
        }

        return $service;
    }

    public static function makeFileSystemHelper(): FileSystemHelper
    {
        return new FileSystemHelper();
    }
}
