<?php
declare(strict_types=1);


namespace TestSeparator\Strategy\FilePath;


class FilePathByFileSystemHelper implements TestFilePathInterface
{
    use BaseTestDirPathTrait;

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



}
