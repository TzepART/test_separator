<?php
declare(strict_types=1);

namespace TestSeparator\Model;

class ItemTestInfo
{
    /**
     * @var string
     */
    private $relativeParentDirectoryPath;

    /**
     * @var string
     */
    private $testFilePath;

    /**
     * @var string
     */
    private $relativeTestFilePath;

    /**
     * @var string
     */
    private $testName;

    /**
     * @var int
     */
    private $timeExecuting;


    /**
     * ItemTestInfo constructor.
     *
     * @param string $relativeParentDirectoryPath
     * @param string $testFilePath
     * @param string $relativeTestFilePath
     * @param string $testName
     * @param int $timeExecuting
     */
    public function __construct(
        string $relativeParentDirectoryPath,
        string $testFilePath,
        string $relativeTestFilePath,
        string $testName,
        int $timeExecuting
    )
    {

        $this->relativeParentDirectoryPath = $relativeParentDirectoryPath;
        $this->testFilePath = $testFilePath;
        $this->relativeTestFilePath = $relativeTestFilePath;
        $this->testName = $testName;
        $this->timeExecuting = $timeExecuting;
    }

    /**
     * @return string
     */
    public function getRelativeTestFilePath(): string
    {
        return $this->relativeTestFilePath;
    }

    /**
     * @return string
     */
    public function getTestName(): string
    {
        return $this->testName;
    }

    /**
     * @return int
     */
    public function getTimeExecuting(): int
    {
        return $this->timeExecuting;
    }

    public function asArray(): array
    {
        return [
            $this->relativeParentDirectoryPath,
            $this->testFilePath,
            $this->relativeTestFilePath,
            $this->testName,
            $this->timeExecuting,
        ];
    }
}
