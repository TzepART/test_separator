<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use TestSeparator\Configuration;
use TestSeparator\Strategy\SeparationDepth\DepthDirectoryLevelStrategy;
use TestSeparator\Strategy\SeparationDepth\DepthClassLevelStrategy;
use TestSeparator\Strategy\FilePath\ItemTestCollectionBuilderByByCodeceptionReports;
use TestSeparator\Strategy\FilePath\ItemTestCollectionBuilderByByFileSystem;
use TestSeparator\Strategy\FilePath\ItemTestCollectionBuilderInterface;
use TestSeparator\Strategy\SeparationDepth\DepthLevelStrategyInterface;
use TestSeparator\Strategy\SeparationDepth\DepthMethodLevelStrategy;

class ServicesSeparateTestsFactory
{
    public const DIRECTORY_LEVEL = 'directory';
    public const CLASS_LEVEL     = 'class';
    public const METHOD_LEVEL    = 'method';

    public static function makeLevelDeepService(string $serviceName): DepthLevelStrategyInterface
    {
        if($serviceName === self::DIRECTORY_LEVEL){
            return new DepthDirectoryLevelStrategy();
        }

        if($serviceName === self::CLASS_LEVEL){
            return new DepthClassLevelStrategy();
        }

        if($serviceName === self::METHOD_LEVEL){
            return new DepthMethodLevelStrategy();
        }

        throw new \RuntimeException('DepthLevel is undefined');
    }

    public static function makeTestFilePathHelper(Configuration $configuration): ItemTestCollectionBuilderInterface
    {
        if(is_dir($configuration->getCodeceptionReportDir())){
            return new ItemTestCollectionBuilderByByCodeceptionReports($configuration->getTestsDirectory(), $configuration->getCodeceptionReportDir());
        }

        return new ItemTestCollectionBuilderByByFileSystem($configuration->getTestsDirectory(), $configuration->getAllureReportsDirectory());
    }

    public static function makeConfiguration(string $configPath): Configuration
    {
        return new Configuration($configPath);
    }
}
