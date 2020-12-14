<?php
declare(strict_types=1);

namespace TestSeparator\Service;

use TestSeparator\Configuration;
use TestSeparator\Exception\CodeceptionReportsDirIsEmptyException;
use TestSeparator\Exception\InvalidPathToResultDirectoryException;
use TestSeparator\Exception\InvalidPathToTestsDirectoryException;
use TestSeparator\Exception\NotAvailableDepthLevelException;
use TestSeparator\Exception\PathToCodeceptionReportsDirIsEmptyException;
use TestSeparator\Exception\SuitesDirectoriesCollectionIsEmptyException;
use TestSeparator\Exception\UnknownSeparatingStrategyException;
use TestSeparator\Exception\ValidationOfConfigurationException;
use TestSeparator\Handler\ServicesSeparateTestsFactory;

class ConfigurationValidator
{
    private const AVAILABLE_SEPARATING_STRATEGIES = [
        ServicesSeparateTestsFactory::CODECEPTION_SEPARATING_STRATEGY,
        ServicesSeparateTestsFactory::METHOD_SIZE_SEPARATING_STRATEGY,
    ];

    private const AVAILABLE_DEPTH_LEVELS = [
        ServicesSeparateTestsFactory::DIRECTORY_LEVEL,
        ServicesSeparateTestsFactory::CLASS_LEVEL,
        ServicesSeparateTestsFactory::METHOD_LEVEL,
    ];

    private const NOT_AVAILABLE_DEPTH_LEVEL_WAS_GOT = 'Not available depth level was got.';
    private const PATH_TO_TESTS_DIRECTORY_IS_INVALID = 'Path to tests directory is Invalid.';
    private const PATH_TO_RESULTS_DIRECTORY_IS_INVALID = 'Path to results directory is Invalid.';
    private const THERE_WAS_GOT_UNKNOWN_SEPARATING_STRATEGY = 'There was got unknown separating strategy.';
    private const TESTS_SUITES_DIRECTORIES_COLLECTION_IS_EMPTY = 'Tests suites directories Collection is empty.';
    private const PATH_TO_CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY = 'Path to Codeception Reports directory is empty.';
    private const CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY = 'Codeception Reports directory is empty.';


    public function validate(Configuration $configuration): void
    {
        if (!is_dir($configuration->getTestsDirectory())) {
            throw new InvalidPathToTestsDirectoryException(self::PATH_TO_TESTS_DIRECTORY_IS_INVALID);
        }

        if (!is_dir($configuration->getResultPath())) {
            throw new InvalidPathToResultDirectoryException(self::PATH_TO_RESULTS_DIRECTORY_IS_INVALID);
        }

        if (!in_array($configuration->getDepthLevel(), self::AVAILABLE_DEPTH_LEVELS, true)) {
            throw new NotAvailableDepthLevelException(self::NOT_AVAILABLE_DEPTH_LEVEL_WAS_GOT);
        }

        try {
            $this->validateConfigurationForSeparatingByReports($configuration);
        } catch (ValidationOfConfigurationException $e) {
            if ($configuration->isUseDefaultSeparatingStrategy()) {
                $configuration->setSeparatingStrategy(ServicesSeparateTestsFactory::METHOD_SIZE_SEPARATING_STRATEGY);
            } else {
                throw $e;
            }
        }

        $separatingStrategy = $configuration->getSeparatingStrategy();
        if ($separatingStrategy === ServicesSeparateTestsFactory::METHOD_SIZE_SEPARATING_STRATEGY) {
            if (count($configuration->getTestSuitesDirectories()) === 0) {
                throw new SuitesDirectoriesCollectionIsEmptyException(self::TESTS_SUITES_DIRECTORIES_COLLECTION_IS_EMPTY);
            }
            //TODO add validation that all Tests Suites Directories contain tests (?)
        }
    }

    /**
     * @param Configuration $configuration
     */
    private function validateConfigurationForSeparatingByReports(Configuration $configuration): void
    {
        if (!in_array($configuration->getSeparatingStrategy(), self::AVAILABLE_SEPARATING_STRATEGIES, true)) {
            throw new UnknownSeparatingStrategyException(self::THERE_WAS_GOT_UNKNOWN_SEPARATING_STRATEGY);
        }

        if ($configuration->getSeparatingStrategy() === ServicesSeparateTestsFactory::CODECEPTION_SEPARATING_STRATEGY) {
            if ($configuration->getCodeceptionReportsDir() === '') {
                throw new PathToCodeceptionReportsDirIsEmptyException(self::PATH_TO_CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY);
            }
            if (!FileSystemHelper::checkFilesInDir($configuration->getCodeceptionReportsDir())) {
                throw new CodeceptionReportsDirIsEmptyException(self::CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY);
            }
        }
    }
}
