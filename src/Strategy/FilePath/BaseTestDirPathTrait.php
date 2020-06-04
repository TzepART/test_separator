<?php
declare(strict_types=1);


namespace TestSeparator\Strategy\FilePath;


trait BaseTestDirPathTrait
{
    /**
     * @var string
     */
    private $baseTestDirPath;

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

    public function getFilePathsByDirectory(string $workDir): array
    {
        $files     = scandir($workDir);
        $filePaths = [];
        foreach ($files as $file) {
            $filePath = $workDir . $file;
            if (is_file($filePath)) {
                $filePaths[] = $filePath;
            }
        }

        return $filePaths;
    }
}
