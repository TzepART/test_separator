<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\ItemTestsBuildings;

use TestSeparator\Model\ItemTestInfo;

class ItemTestCollectionBuilderByByFileSystem extends AbstractItemTestCollectionBuilder
{
    /**
     * @var string
     */
    private $allureReportsDirectory;

    /**
     * ItemTestCollectionBuilderByByFileSystem constructor.
     *
     * @param string $baseTestDirPath
     * @param string $allureReportsDirectory
     */
    public function __construct(string $baseTestDirPath, string $allureReportsDirectory)
    {
        parent::__construct($baseTestDirPath);
        $this->allureReportsDirectory = $allureReportsDirectory;
    }

    /**
     * @return ItemTestInfo[]|array
     */
    public function buildTestInfoCollection(): array
    {
        $filePaths = $this->getFilePathsByDirectory($this->allureReportsDirectory);

        $results = [];
        foreach ($filePaths as $filePath) {
            echo $filePath . PHP_EOL;
            //TODO add catching if xml is Invalid
            $xml = simplexml_load_string(file_get_contents($filePath));

            preg_match('/Support\.([a-z]+)/', (string) $xml->name, $suitMatches);
            $dir = $suitMatches[1];
            foreach ($xml->{'test-cases'}->children() as $child) {
                preg_match('/([^ ]+)/', (string) $child->name, $matches);
                $test = $matches[1];
                $time = (int) ($child->attributes()->stop - $child->attributes()->start);
                $file = $this->getFilePathByTestName($test, $dir);
                if ($file !== '') {
                    $relativePath = str_replace($this->getBaseTestDirPath(), 'tests/', $file);
                    $results[]    = new ItemTestInfo($dir, $file, $relativePath, $test, $time);
                }
            }
        }

        return $results;
    }

    /**
     * @param string $testName
     * @param string $parentDir
     *
     * @return string
     */
    private function getFilePathByTestName(string $testName, string $parentDir): string
    {
        $patternCommand = 'grep -R "%s" -l ' . $this->getBaseTestDirPath() . '%s | head -1';
        $file           = shell_exec(sprintf($patternCommand, $testName, $parentDir)) ?? '';

        return trim($file);
    }


}
