<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use Symfony\Component\Finder\Finder;
use TestSeparator\Model\ItemTestInfo;

class ItemTestCollectionBuilderByMethodSize extends AbstractItemTestCollectionBuilder
{
    /**
     * @var array
     */
    private $testSuitesDirectories;

    /**
     * @var Finder
     */
    private $fileFinder;

    /**
     * AbstractItemTestCollectionBuilder constructor.
     *
     * @param string $baseTestDirPath
     * @param array $testSuitesDirectories
     */
    public function __construct(string $baseTestDirPath, array $testSuitesDirectories)
    {
        parent::__construct($baseTestDirPath);
        $this->testSuitesDirectories = $testSuitesDirectories;
        $this->fileFinder            = new Finder();
    }

    public function buildTestInfoCollection(): array
    {
        $results = [];

        foreach ($this->testSuitesDirectories as $index => $testSuitesDirectory) {
            $filePaths = $this->fileFinder->files()->in($this->getBaseTestDirPath() . $testSuitesDirectory . '/');

            foreach ($filePaths as $filePath) {
                $testFilePath = $filePath->getRealPath();
                preg_match_all('/public function (test[^(]+)[^{]+\{([^{]+)}/', file_get_contents($testFilePath), $matches);

                if (isset($matches[1]) && is_array($matches[1])) {
                    $relativeTestFilePath        = str_replace($this->getBaseTestDirPath(), 'tests/', $filePath->getRealPath());
                    $relativeParentDirectoryPath = str_replace($this->getBaseTestDirPath(), 'tests/', $filePath->getPath()) . '/';
                    foreach ($matches[1] as $testNumber => $testName) {
                        $testSize  = $matches[2][$testNumber] ? strlen($matches[2][$testNumber]) : 0;
                        $results[] = new ItemTestInfo(
                            $relativeParentDirectoryPath,
                            $testFilePath,
                            $relativeTestFilePath,
                            $testName,
                            $testSize
                        );
                    }
                }
            }
        }

        return $results;
    }
}
