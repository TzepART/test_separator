<?php
declare(strict_types=1);


namespace TestSeparator\Strategy\FilePath;


interface TestFilePathInterface
{
    public function getFilePathByTestName(string $testName, string $parentDir): string;

    public function getFilePathsByDirectory(string $workDir): array;

    public function getBaseTestDirPath(): string;

    public function setBaseTestDirPath(string $baseTestDirPath);
}
