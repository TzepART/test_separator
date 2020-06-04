<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;
use TestSeparator\Configuration;
use TestSeparator\Model\GroupBlockInfo;
use TestSeparator\Model\TestInfo;
use TestSeparator\Strategy\FilePath\TestFilePathInterface;

class SeparateTestsHandler
{
    /**
     * @var TestFilePathInterface
     */
    private $fileSystemHelper;

    /**
     * @var string
     */
    private $baseTestDirPath;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param TestFilePathInterface $fileSystemHelper
     * @param Configuration $configuration
     *
     */
    public function __construct(TestFilePathInterface $fileSystemHelper, Configuration $configuration)
    {
        $this->fileSystemHelper = $fileSystemHelper;
        $this->configuration    = $configuration;
        $this->setBaseTestDirPath($configuration->getTestsDirectory());
    }

    public function buildTestInfoCollection(): array
    {
        $filePaths = $this->getFilePaths($this->configuration->getAllureReportsDirectory());

        $results = [];
        foreach ($filePaths as $filePath) {
            echo $filePath . PHP_EOL;
            $xml = simplexml_load_string(file_get_contents($filePath));

            preg_match('/Support\.([a-z]+)/', (string) $xml->name, $suitMatches);
            $dir = $suitMatches[1];
            foreach ($xml->{'test-cases'}->children() as $child) {
                preg_match('/([^ ]+)/', (string) $child->name, $matches);
                $test = $matches[1];
                $time = (int) ($child->attributes()->stop - $child->attributes()->start);
                $file = $this->fileSystemHelper->getFilePathByTestName($test, $dir);
                if ($file !== '') {
                    $relativePath = str_replace($this->configuration->getTestsDirectory(), 'tests/', $file);
                    $results[]    = new TestInfo($dir, $file, $relativePath, $test, $time);
                }
            }
        }

        return $results;
    }

    private function getFilePaths(string $workDir): array
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

    /**
     * @param TestInfo[] $testInfoItems
     *
     * @return array
     */
    public function summTimeByDirectories(array $testInfoItems): array
    {
        $timeResults = [];
        foreach ($testInfoItems as $testInfoItem) {
            $rootDir = $testInfoItem->getDir();
            preg_match('/([A-Za-z]+)\//', trim(str_replace($this->getBaseTestDirPath() . $rootDir . '/', '', $testInfoItem->getFile())), $matches);
            $dir    = $matches[1];
            $keyDir = $rootDir . '/' . $dir . '/' . $testInfoItem->getFile();
            $time   = round(((int) $testInfoItem->getTime()) / 1000, 2);
            if (isset($timeResults[$keyDir])) {
                $timeResults[$keyDir] = round($timeResults[$keyDir] + $time, 2);
            } else {
                $timeResults[$keyDir] = $time;
            }
        }

        return $timeResults;
    }

    /**
     * @param TestInfo[] $testInfoItems
     *
     * @return array
     */
    public function summTimeByFiles(array $testInfoItems): array
    {
        $timeResults = [];
        foreach ($testInfoItems as $testInfoItem) {
            $time = round(($testInfoItem->getTime()) / 1000, 2);
            if (isset($timeResults[$testInfoItem->getRelativePath()])) {
                $timeResults[$testInfoItem->getRelativePath()] = round($timeResults[$testInfoItem->getRelativePath()] + $time, 2);
            } else {
                $timeResults[$testInfoItem->getRelativePath()] = $time;
            }
        }

        return $timeResults;
    }


    /**
     * @param array $timeResults
     * @param int $countSuit
     *
     * @return array
     */
    public function separateDirectoriesByTime(array $timeResults, int $countSuit): array
    {
        $greedy = new Greedy();
        $greedy->setData($timeResults);
        $greedy->setSize($countSuit);
        $result = $greedy->getResult();

        $groupBlockInfoItems = [];
        foreach ($result as $key => $block) {
            $groupBlockInfo = new GroupBlockInfo();
            foreach ($block as $time) {
                $keyDir = array_search($time, $timeResults);
                $groupBlockInfo->addDirTime($keyDir, $time);
            }
            $groupBlockInfo->setSummTime(array_sum($block));
            $groupBlockInfoItems[] = $groupBlockInfo;
        }

        return $groupBlockInfoItems;
    }

    public function removeAllGroupFiles(): void
    {
        $files = scandir($this->configuration->getResultPath()); // get all file names
        foreach ($files as $file) { // iterate files
            $filePath = $this->configuration->getResultPath() . $file;
            if (is_file($filePath)) {
                unlink($filePath); // delete file
            }
        }
    }

    public function getBaseTestDirPath(): string
    {
        return $this->baseTestDirPath;
    }

    /**
     * TODO change on setting by config
     *
     * @param string $baseTestDirPath
     *
     * @return $this
     */
    public function setBaseTestDirPath(string $baseTestDirPath): self
    {
        $this->baseTestDirPath = $baseTestDirPath;
        $this->fileSystemHelper->setBaseTestDirPath($baseTestDirPath);

        return $this;
    }

    public function getGroupDirectoryPath(): string
    {
        return $this->configuration->getResultPath();
    }
}
