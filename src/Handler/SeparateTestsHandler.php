<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;
use TestSeparator\Model\GroupBlockInfo;
use TestSeparator\Model\TestInfo;
use TestSeparator\Strategy\LevelDeepStrategyInterface;


class SeparateTestsHandler
{
    /**
     * @var FileSystemHelper
     */
    private $fileSystemHelper;

    /**
     * @var LevelDeepStrategyInterface
     */
    private $levelDeepHelper;

    /**
     * @param FileSystemInterface        $fileSystemHelper
     * @param LevelDeepStrategyInterface $levelDeepHelper
     *
     */
    public function __construct(FileSystemInterface $fileSystemHelper, LevelDeepStrategyInterface $levelDeepHelper)
    {
        $this->fileSystemHelper = $fileSystemHelper;
        $this->levelDeepHelper = $levelDeepHelper;
    }

    /**
     * @param string $suitesFile
     * @param string $baseTestDirPath
     *
     * @return TestInfo[] | array
     */
    public function reFormateSuitesFile(string $suitesFile, string $baseTestDirPath): array
    {
        $strings = file($suitesFile);
        $results = [];

        // remove head line
        array_shift($strings);

        foreach ($strings as $index => $string) {
            $string   = str_replace('"', '', $string);
            $strArray = explode(',', $string);
            preg_match('/Support\.([a-z]+)\.([a-zA-Z]+)/', $strArray[1], $matches);
            $dir       = $matches[1];
            $test      = $matches[2];
            $time      = (int) $strArray[2];
            $file      = $this->fileSystemHelper->getTestFilePath($baseTestDirPath, $test, $dir);
            $results[] = new TestInfo($dir, $file, $test, $time);
        }

        return $results;
    }


    /**
     * @param TestInfo[] $testInfoItems
     * @param string     $baseTestDirPath
     *
     * @return array
     */
    public function summTimeByDirectories(array $testInfoItems, string $baseTestDirPath): array
    {
        $timeResults = [];
        $summ        = 0;
        foreach ($testInfoItems as $testInfoItem) {
            $rootDir = $testInfoItem->getDir();
            preg_match('/([A-Za-z]+)\//', trim(str_replace($baseTestDirPath . $rootDir . '/', '', $testInfoItem->getFile())), $matches);
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
     * @param int   $countSuit
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
}
