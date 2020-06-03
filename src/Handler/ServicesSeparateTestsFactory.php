<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use TestSeparator\Strategy\DirectoryDeepStrategyService;
use TestSeparator\Strategy\FileDeepStrategyService;
use TestSeparator\Strategy\FilePath\FilePathByFileSystemHelper;
use TestSeparator\Strategy\FilePath\TestFilePathInterface;
use TestSeparator\Strategy\LevelDeepStrategyInterface;
use TestSeparator\Strategy\TestDeepStrategyService;

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
                $service = new FileDeepStrategyService();
                break;
            case self::TEST_LEVEL:
                $service = new TestDeepStrategyService();
                break;
            default:
                $service = new DirectoryDeepStrategyService();
                break;
        }

        return $service;
    }

    public static function makeTestFilePathHelper(string $serviceName = ''): TestFilePathInterface
    {
        return new FilePathByFileSystemHelper();
    }
}
