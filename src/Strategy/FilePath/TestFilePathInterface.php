<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\FilePath;

interface TestFilePathInterface
{
    public function buildTestInfoCollection(): array;

    public function getBaseTestDirPath(): string;

    public function setBaseTestDirPath(string $baseTestDirPath);
}
