<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;
use TestSeparator\Model\GroupBlockInfo;
use TestSeparator\Model\TestInfo;
use TestSeparator\Strategy\FilePath\TestFilePathInterface;
use TestSeparator\Strategy\LevelDeepStrategyInterface;


class SeparateTestsHandler
{
    /**
     * @var TestFilePathInterface
     */
    private $fileSystemHelper;

    /**
     * @var LevelDeepStrategyInterface
     */
    private $levelDeepHelper;

    /**
     * @var string
     */
    private $baseTestDirPath;

    /**
     * @param TestFilePathInterface $fileSystemHelper
     * @param LevelDeepStrategyInterface $levelDeepHelper
     *
     */
    public function __construct(TestFilePathInterface $fileSystemHelper, LevelDeepStrategyInterface $levelDeepHelper)
    {
        $this->fileSystemHelper = $fileSystemHelper;
        $this->levelDeepHelper  = $levelDeepHelper;
    }

    public function buildTestInfoCollection(string $workDir): array
    {
        $filePaths = $this->getFilePaths($workDir);

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
                $results[] = new TestInfo($dir, $file, $test, $time);
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
        $summ        = 0;
        foreach ($testInfoItems as $testInfoItem) {
            $rootDir = $testInfoItem->getDir();
            preg_match('/([A-Za-z]+)\//', trim(str_replace($this->getBaseTestDirPath() . $rootDir . '/', '', $testInfoItem->getFile())), $matches);
            $dir    = $matches[1];
            $keyDir = $rootDir . '/' . $dir;
            $time   = round(((int) $testInfoItem->getTime()) / 1000, 2);
            if (isset($timeResults[$keyDir])) {
                $timeResults[$keyDir] = round($timeResults[$keyDir] + $time, 2);
            } else {
                $timeResults[$keyDir] = $time;
            }
            $summ += $time;
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

    /**
     * @param string $groupDirPath
     */
    public function removeAllGroupFiles(string $groupDirPath): void
    {
        $files = scandir($groupDirPath); // get all file names
        foreach ($files as $file) { // iterate files
            $filePath = $groupDirPath . $file;
            if (is_file($filePath)) {
                unlink($filePath); // delete file
            }
        }
    }

    /**
     * @return string
     */
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
    public function setBaseTestDirPath(string $baseTestDirPath)
    {
        $this->baseTestDirPath = $baseTestDirPath;
        $this->fileSystemHelper->setBaseTestDirPath($baseTestDirPath);

        return $this;
    }
}
