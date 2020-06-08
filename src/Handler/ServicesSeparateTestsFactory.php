<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use TestSeparator\Configuration;
use TestSeparator\Service\FileSystemHelper;
use TestSeparator\Strategy\ItemTestsBuildings\ItemTestCollectionBuilderByMethodSize;
use TestSeparator\Strategy\SeparationDepth\DepthDirectoryLevelStrategy;
use TestSeparator\Strategy\SeparationDepth\DepthClassLevelStrategy;
use TestSeparator\Strategy\ItemTestsBuildings\ItemTestCollectionBuilderByCodeceptionReports;
use TestSeparator\Strategy\ItemTestsBuildings\ItemTestCollectionBuilderByAllureReports;
use TestSeparator\Strategy\ItemTestsBuildings\ItemTestCollectionBuilderInterface;
use TestSeparator\Strategy\SeparationDepth\DepthLevelStrategyInterface;
use TestSeparator\Strategy\SeparationDepth\DepthMethodLevelStrategy;

class ServicesSeparateTestsFactory
{
    public const CODECEPTION_SEPARATING_STRATEGY    = 'codeception-report';
    public const ALLURE_REPORTS_SEPARATING_STRATEGY = 'allure-report';
    public const METHOD_SIZE_SEPARATING_STRATEGY    = 'method-size';

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
        if(FileSystemHelper::checkFilesInDir($configuration->getCodeceptionReportsDir())){
            return new ItemTestCollectionBuilderByCodeceptionReports(
                $configuration->getTestsDirectory(),
                $configuration->getCodeceptionReportsDir()
            );
        }

        if(FileSystemHelper::checkFilesInDir($configuration->getAllureReportsDirectory())){
            return new ItemTestCollectionBuilderByAllureReports(
                $configuration->getTestsDirectory(),
                $configuration->getAllureReportsDirectory(),
                $configuration->getTestSuitesDirectories()
            );
        }

        return new ItemTestCollectionBuilderByMethodSize(
            $configuration->getTestsDirectory(),
            $configuration->getTestSuitesDirectories()
        );
    }
}
