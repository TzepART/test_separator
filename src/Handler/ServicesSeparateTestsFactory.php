<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use TestSeparator\Configuration;
use TestSeparator\Strategy\DirectoryDeepStrategyService;
use TestSeparator\Strategy\ClassDeepStrategyService;
use TestSeparator\Strategy\FilePath\FilePathByFileSystemHelper;
use TestSeparator\Strategy\FilePath\TestFilePathInterface;
use TestSeparator\Strategy\LevelDeepStrategyInterface;
use TestSeparator\Strategy\MethodDeepStrategyService;

class ServicesSeparateTestsFactory
{
    public const DIRECTORY_LEVEL = 'directory';
    public const CLASS_LEVEL     = 'class';
    public const METHOD_LEVEL    = 'method';

    public static function makeLevelDeepService(string $serviceName): LevelDeepStrategyInterface
    {
        if($serviceName === self::DIRECTORY_LEVEL){
            return new DirectoryDeepStrategyService();
        }

        if($serviceName === self::CLASS_LEVEL){
            return new ClassDeepStrategyService();
        }

        if($serviceName === self::METHOD_LEVEL){
            return new MethodDeepStrategyService();
        }

        throw new \RuntimeException('DepthLevel is undefined');
    }

    public static function makeTestFilePathHelper(string $serviceName = ''): TestFilePathInterface
    {
        return new FilePathByFileSystemHelper();
    }

    public static function makeConfiguration(string $configPath): Configuration
    {
        return new Configuration($configPath);
    }
}
