<?php
declare(strict_types=1);


namespace TestSeparator\Strategy\FilePath;


interface TestFilePathInterface
{
    /**
     * @param string $testName
     * @param string $parentDir
     *
     * @return string
     */
    public function getFilePathByTestName(string $testName, string $parentDir): string;

    /**
     * @return string
     */
    public function getBaseTestDirPath(): string;

    /**
     * @param string $baseTestDirPath
     */
    public function setBaseTestDirPath(string $baseTestDirPath);
}
