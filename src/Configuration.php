<?php
declare(strict_types=1);

namespace TestSeparator;

class Configuration
{
    public const SEPARATING_STRATEGY_KEY = 'separating-strategy';
    public const USE_DEFAULT_SEPARATING_STRATEGY_KEY = 'use-default-separating-strategy';
    public const CODECEPTION_REPORTS_DIRECTORY_KEY = 'codeception-reports-directory';
    public const TESTS_DIRECTORY_KEY = 'tests-directory';
    public const RESULT_PATH_KEY = 'result-path';
    public const LEVEL_KEY = 'level';
    public const TEST_SUITES_DIRECTORIES_KEY = 'test-suites-directories';
    public const DEFAULT_SEPARATING_STRATEGIES_KEY = 'default-separating-strategies';
    public const DEFAULT_GROUPS_DIRECTORY_KEY = 'default-groups-directory';

    /**
     * @var string
     */
    private $separatingStrategy;

    /**
     * @var bool
     */
    private $useDefaultSeparatingStrategy;

    /**
     * @var string
     */
    private $codeceptionReportsDir;

    /**
     * @var string
     */
    private $testsDirectory;

    /**
     * @var string
     */
    private $resultPath;

    /**
     * @var string
     */
    private $depthLevel;

    /**
     * @var array
     */
    private $testSuitesDirectories;

    /**
     * @var array
     */
    private $defaultSeparatingStrategies;

    /**
     * @var string
     */
    private $defaultGroupsDir;

    /**
     * Configuration constructor.
     *
     * @param array $config
     *
     * TODO Checking of fields types
     */
    public function __construct(array $config)
    {
        $this->separatingStrategy = $config[self::SEPARATING_STRATEGY_KEY] ?? '';
        $this->useDefaultSeparatingStrategy = $config[self::USE_DEFAULT_SEPARATING_STRATEGY_KEY] ?? false;
        $this->codeceptionReportsDir = $config[self::CODECEPTION_REPORTS_DIRECTORY_KEY] ?? '';
        $this->testsDirectory = $config[self::TESTS_DIRECTORY_KEY] ?? '';
        $this->resultPath = $config[self::RESULT_PATH_KEY] ?? '';
        $this->depthLevel = $config[self::LEVEL_KEY] ?? '';
        $this->testSuitesDirectories = $config[self::TEST_SUITES_DIRECTORIES_KEY] ?? [];
        $this->defaultSeparatingStrategies = $config[self::DEFAULT_SEPARATING_STRATEGIES_KEY] ?? [];
        $this->defaultGroupsDir = $config[self::DEFAULT_GROUPS_DIRECTORY_KEY] ?? '';
    }


    /**
     * @return string
     */
    public function getSeparatingStrategy(): string
    {
        return $this->separatingStrategy;
    }

    /**
     * @param string $separatingStrategy
     */
    public function setSeparatingStrategy(string $separatingStrategy): void
    {
        $this->separatingStrategy = $separatingStrategy;
    }

    /**
     * @return bool
     */
    public function isUseDefaultSeparatingStrategy(): bool
    {
        return $this->useDefaultSeparatingStrategy;
    }

    /**
     * @return string
     */
    public function getTestsDirectory(): string
    {
        return $this->testsDirectory;
    }

    /**
     * @return string
     */
    public function getCodeceptionReportsDir(): string
    {
        return $this->codeceptionReportsDir;
    }

    /**
     * @return string
     */
    public function getResultPath(): string
    {
        return $this->resultPath;
    }

    /**
     * @return string
     */
    public function getDepthLevel(): string
    {
        return $this->depthLevel;
    }

    /**
     * @return array
     */
    public function getTestSuitesDirectories(): array
    {
        return $this->testSuitesDirectories;
    }

    /**
     * @return array
     */
    public function getDefaultSeparatingStrategies(): array
    {
        return $this->defaultSeparatingStrategies;
    }

    /**
     * @return string
     */
    public function getDefaultGroupsDir(): string
    {
        return $this->defaultGroupsDir;
    }
}
