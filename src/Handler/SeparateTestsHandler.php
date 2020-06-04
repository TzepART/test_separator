<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;
use TestSeparator\Configuration;
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
     * @var string
     */
    private $baseTestDirPath;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LevelDeepStrategyInterface
     */
    private $timeCounterStrategy;

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
        $this->timeCounterStrategy = ServicesSeparateTestsFactory::makeLevelDeepService($configuration->getDepthLevel());
    }

    public function buildTestInfoCollection(): array
    {
        $filePaths = $this->fileSystemHelper->getFilePathsByDirectory($this->configuration->getAllureReportsDirectory());

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
                $file = $this->fileSystemHelper->getFilePathByTestName($test, $dir);
                if ($file !== '') {
                    $relativePath = str_replace($this->configuration->getTestsDirectory(), 'tests/', $file);
                    $results[]    = new TestInfo($dir, $file, $relativePath, $test, $time);
                }
            }
        }

        return $results;
    }

    public function groupTimeEntityWithCountedTime($testInfoItems): array
    {
        return $this->timeCounterStrategy->groupTimeEntityWithCountedTime($testInfoItems);
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
                $keyDir = array_search($time, $timeResults, true);
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
     * @param string $baseTestDirPath
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
