<?php
declare(strict_types=1);


namespace TestSeparator\Handler;


interface FileSystemInterface
{

    /**
     * @param string $baseTestDirPath
     * @param        $test
     * @param        $dir
     *
     * @return string
     */
    public function getTestFilePath(string $baseTestDirPath, $test, $dir): string;
}
