<?php
declare(strict_types=1);


namespace TestSeparator\Handler;


class FileSystemHelper implements FileSystemInterface
{

    /**
     * @param string $baseTestDirPath
     * @param        $test
     * @param        $dir
     *
     * @return string
     */
    public function getTestFilePath(string $baseTestDirPath, $test, $dir): string
    {
        $patternCommand = 'grep -R "%s" -l ' . $baseTestDirPath . '%s | head -1';
        $file           = trim(shell_exec(sprintf($patternCommand, $test, $dir)));

        return $file;
    }
}
