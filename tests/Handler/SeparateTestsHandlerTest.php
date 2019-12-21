<?php
declare(strict_types=1);

namespace TestSeparator\Tests\Handler;

use PHPUnit\Framework\TestCase;
use TestSeparator\Strategy\DirectoryDeepStrategyService;
use TestSeparator\Handler\FileSystemHelper;
use TestSeparator\Handler\SeparateTestsHandler;
use TestSeparator\Model\GroupBlockInfo;
use TestSeparator\Model\TestInfo;

class SeparateTestsHandlerTest extends TestCase
{
    /**
     * @var SeparateTestsHandler
     */
    private $handler;

    protected function setUp()
    {
        parent::setUp();

        $fileSystemHelper = $this->createMock(FileSystemHelper::class);
        $mockResults      = json_decode(file_get_contents(__DIR__ . '/../fixtures/file-system-mock.json'), true);

        /** @var FileSystemHelper $fileSystemHelper */
        $fileSystemHelper->method('getTestFilePath')
            ->will(
                $this->returnCallback(
                    function ($baseTestDirPath, $test, $dir) use ($mockResults) {
                        return $mockResults[$baseTestDirPath . '+' . $test . '+' . $dir];
                    }
                )
            );
        /** @var DirectoryDeepStrategyService $levelDeepHelper */
        $levelDeepHelper = $this->createMock(DirectoryDeepStrategyService::class);

        $this->handler = new SeparateTestsHandler($fileSystemHelper, $levelDeepHelper);
    }

    /**
     * @dataProvider reFormateSuitesFileData()
     *
     * @param string $suitesFile
     * @param string $expectedResultFile
     * @param string $baseTestDirPath
     */
    public function testReFormateSuitesFile(string $suitesFile, string $expectedResultFile, string $baseTestDirPath)
    {

        $resultArray = $this->handler->reFormateSuitesFile($suitesFile, $baseTestDirPath);

        $this->assertEquals(
            array_map(
                function (TestInfo $testInfo) {
                    return $testInfo->asArray();
                },
                $resultArray
            ),
            array_map(
                function (string $string) {
                    return explode(';', trim($string));
                },
                file($expectedResultFile)
            )
        );
    }

    public function reFormateSuitesFileData()
    {
        return [
            [
                __DIR__ . '/../fixtures/suites.csv',
                __DIR__ . '/../fixtures/results/result.csv',
                '/path/to/project/tests/',
            ],
        ];
    }

    /**
     * @dataProvider summTimeByDirectoriesData()
     *
     * @param string $inputResultFile
     * @param string $expectedTimeResultsFile
     * @param string $baseTestDirPath
     */
    public function testSummTimeByDirectories(string $inputResultFile, string $expectedTimeResultsFile, string $baseTestDirPath)
    {
        $testInfoItems    = array_map(
            function (string $string) {
                $testInfoArray = explode(';', trim($string));

                return new TestInfo($testInfoArray[0], $testInfoArray[1], $testInfoArray[2], (int) $testInfoArray[3]);
            },
            file($inputResultFile)
        );
        $testDirsWithTime = $this->handler->summTimeByDirectories($testInfoItems, $baseTestDirPath);
        $this->assertEquals($testDirsWithTime, json_decode(file_get_contents($expectedTimeResultsFile), true));
    }

    public function summTimeByDirectoriesData()
    {
        return [
            [
                __DIR__ . '/../fixtures/results/result.csv',
                __DIR__ . '/../fixtures/results/time_results.json',
                '/path/to/project/tests/',
            ],
        ];
    }

    /**
     * @dataProvider separateDirectoriesByTimeData()
     *
     * @param string $timeResultsFile
     * @param string $expectedGroupSeparatingFile
     * @param int    $countSuit
     */
    public function testSeparateDirectoriesByTime(string $timeResultsFile, string $expectedGroupSeparatingFile, int $countSuit)
    {
        $testInfoItems = json_decode(file_get_contents($timeResultsFile), true);

        $arGroups = $this->handler->separateDirectoriesByTime($testInfoItems, $countSuit);

        $this->assertEquals(
            array_map(
                function (GroupBlockInfo $testInfo) {
                    return $testInfo->asArray();
                },
                $arGroups
            ),
            json_decode(file_get_contents($expectedGroupSeparatingFile), true)
        );
    }

    public function separateDirectoriesByTimeData()
    {
        return [
            [
                __DIR__ . '/../fixtures/results/time_results.json',
                __DIR__ . '/../fixtures/results/time_results_6_sutecases.json',
                6,
            ],
        ];
    }
}
