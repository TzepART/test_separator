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

    private const NOT_AVAILABLE_DEPTH_LEVEL_WAS_GOT              = 'Not available depth level was got.';
    private const PATH_TO_TESTS_DIRECTORY_IS_INVALID             = 'Path to tests directory is Invalid.';
    private const PATH_TO_RESULTS_DIRECTORY_IS_INVALID           = 'Path to results directory is Invalid.';
    private const THERE_WAS_GOT_UNKNOWN_SEPARATING_STRATEGY      = 'There was got unknown separating strategy.';
    private const TESTS_SUITES_DIRECTORIES_COLLECTION_IS_EMPTY   = 'Tests suites directories Collection is empty.';
    private const PATH_TO_CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY = 'Path to Codeception Reports directory is empty.';
    private const CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY         = 'Codeception Reports directory is empty.';


    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * ConfigurationValidator constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function validate(): void
    {
        if (!is_dir($this->configuration->getTestsDirectory())) {
            throw new InvalidPathToTestsDirectoryException(self::PATH_TO_TESTS_DIRECTORY_IS_INVALID);
        }

        if (!is_dir($this->configuration->getResultPath())) {
            throw new InvalidPathToResultDirectoryException(self::PATH_TO_RESULTS_DIRECTORY_IS_INVALID);
        }

        if (!in_array($this->configuration->getDepthLevel(), self::AVAILABLE_DEPTH_LEVELS, true)) {
            throw new NotAvailableDepthLevelException(self::NOT_AVAILABLE_DEPTH_LEVEL_WAS_GOT);
        }

        try {
            $this->validateConfigurationForSeparatingByReports();
        } catch (ValidationOfConfigurationException $e) {
            if ($this->configuration->isUseDefaultSeparatingStrategy()) {
                $this->configuration->setSeparatingStrategy(ServicesSeparateTestsFactory::METHOD_SIZE_SEPARATING_STRATEGY);
            } else {
                throw $e;
            }
        }

        $separatingStrategy = $this->configuration->getSeparatingStrategy();
        if ($separatingStrategy === ServicesSeparateTestsFactory::METHOD_SIZE_SEPARATING_STRATEGY) {
            if (count($this->configuration->getTestSuitesDirectories()) === 0) {
                throw new SuitesDirectoriesCollectionIsEmptyException(self::TESTS_SUITES_DIRECTORIES_COLLECTION_IS_EMPTY);
            }
            //TODO add validation that all Tests Suites Directories contain tests (?)
        }
    }

    /**
     * @throws ValidationOfConfigurationException
     */
    private function validateConfigurationForSeparatingByReports(): void
    {
        if (!in_array($this->configuration->getSeparatingStrategy(), self::AVAILABLE_SEPARATING_STRATEGIES, true)) {
            throw new UnknownSeparatingStrategyException(self::THERE_WAS_GOT_UNKNOWN_SEPARATING_STRATEGY);
        }

        if ($this->configuration->getSeparatingStrategy() === ServicesSeparateTestsFactory::CODECEPTION_SEPARATING_STRATEGY) {
            if ($this->configuration->getCodeceptionReportsDir() === '') {
                throw new PathToCodeceptionReportsDirIsEmptyException(self::PATH_TO_CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY);
            }
            if (!FileSystemHelper::checkFilesInDir($this->configuration->getCodeceptionReportsDir())) {
                throw new CodeceptionReportsDirIsEmptyException(self::CODECEPTION_REPORTS_DIRECTORY_IS_EMPTY);
            }
        }
    }
}
