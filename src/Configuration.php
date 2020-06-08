<?php
declare(strict_types=1);

namespace TestSeparator;

class Configuration
{
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
    private $allureReportsDirectory;

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
     * Configuration constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->separatingStrategy           = $config['separating-strategy'] ?? '';
        $this->useDefaultSeparatingStrategy = $config['use-default-separating-strategy'] ?? false;
        $this->allureReportsDirectory       = $config['allure-reports-directory'] ?? '';
        $this->codeceptionReportsDir        = $config['codeception-reports-directory'] ?? '';
        $this->testsDirectory               = $config['tests-directory'] ?? '';
        $this->resultPath                   = $config['result-path'] ?? '';
        $this->depthLevel                   = $config['level'] ?? '';
        $this->testSuitesDirectories        = $config['test-suites-directories'] ?? [];
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
    public function getAllureReportsDirectory(): string
    {
        return $this->allureReportsDirectory;
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
}
