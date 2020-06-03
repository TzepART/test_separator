<?php
declare(strict_types=1);


namespace TestSeparator\Strategy\FilePath;


class FilePathByClassMapSystem implements TestFilePathInterface
{
    use BaseTestDirPathTrait;

    private $testClassesMap = [];

    public function getFilePathByTestName(string $testName, string $parentDir): string
    {
        // TODO: Implement getFilePathByTestName() method.
    }

    /**
     * @param string $baseTestDirPath
     *
     * @return $this
     */
    public function setBaseTestDirPath(string $baseTestDirPath)
    {
        $this->baseTestDirPath = $baseTestDirPath;
        $this->setTestClassesMap();

        return $this;
    }

    /**
     * @return array
     */
    public function getTestClassesMap(): array
    {
        return $this->testClassesMap;
    }


    /**
     * TODO add logic of creating TestClassesMap
     * Tree with namespaces of test classes and test (as methods)
     * @return $this
     */
    public function setTestClassesMap()
    {
        $this->testClassesMap = [];

        return $this;
    }
}
