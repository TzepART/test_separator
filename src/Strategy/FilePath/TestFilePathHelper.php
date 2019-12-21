<?php
declare(strict_types=1);


namespace TestSeparator\Strategy\FilePath;


class TestFilePathHelper implements TestFilePathInterface
{
    /**
     * @var string
     */
    private $baseTestDirPath;

    /**
     * @param string $testName
     * @param string $parentDir
     *
     * @return string
     */
    public function getFilePathByTestName(string $testName, string $parentDir): string
    {
        $patternCommand = 'grep -R "%s" -l ' . $this->getBaseTestDirPath() . '%s | head -1';
        $file           = trim(shell_exec(sprintf($patternCommand, $testName, $parentDir)));

        return $file;
    }

    /**
     * @return string
     */
    public function getBaseTestDirPath(): string
    {
        return $this->baseTestDirPath;
    }

    /**
     * @param string $baseTestDirPath
     *
     * @return $this
     */
    public function setBaseTestDirPath(string $baseTestDirPath)
    {
        $this->baseTestDirPath = $baseTestDirPath;

        return $this;
    }

}
