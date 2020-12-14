<?php
//declare(strict_types=1);
//
//namespace Tests\Handler;
//
//use PHPUnit\Framework\TestCase;
//use TestSeparator\Strategy\SeparationDepth\DepthDirectoryLevelStrategy;
//use \TestSeparator\Strategy\ItemTestsBuildings\ItemTestCollectionBuilderByAllureReports;
//use TestSeparator\Handler\SeparateTestsHandler;
//use TestSeparator\Model\GroupBlockInfo;
//use TestSeparator\Model\ItemTestInfo;
//
//class SeparateTestsHandlerTest extends TestCase
//{
//    /**
//     * @var SeparateTestsHandler
//     */
//    private $handler;
//
//    protected function setUp()
//    {
//        parent::setUp();
//
//        $fileSystemHelper = $this->createMock(ItemTestCollectionBuilderByAllureReports::class);
//        $mockResults      = json_decode(file_get_contents(__DIR__ . '/../fixtures/file-system-mock.json'), true);
//
//        /** @var ItemTestCollectionBuilderByAllureReports $fileSystemHelper */
//        $fileSystemHelper->method('getFilePathByTestName')
//            ->willReturnCallback(
//                function ($test, $dir) use ($mockResults) {
//                    return $mockResults['/path/to/project/tests/+' . $test . '+' . $dir];
//                }
//            );
//        $fileSystemHelper->method('setBaseTestDirPath');
//
//        /** @var DepthDirectoryLevelStrategy $levelDeepHelper */
//        $levelDeepHelper = $this->createMock(DepthDirectoryLevelStrategy::class);
//
//        $this->handler = new SeparateTestsHandler($fileSystemHelper, $levelDeepHelper);
//        $this->handler->setBaseTestDirPath('/path/to/project/tests/');
//    }
//
//    /**
//     * @dataProvider buildTestInfoCollectionData()
//     *
//     * @param string $reportsDir
//     * @param string $expectedResultFile
//     */
//    public function testBuildTestInfoCollection(string $reportsDir, string $expectedResultFile)
//    {
//        $resultArray = $this->handler->buildTestInfoCollection($reportsDir);
//
//        $this->assertEquals(
//            array_map(
//                function (ItemTestInfo $testInfo) {
//                    return $testInfo->asArray();
//                },
//                $resultArray
//            ),
//            array_map(
//                function (string $string) {
//                    return explode(';', trim($string));
//                },
//                file($expectedResultFile)
//            )
//        );
//    }
//
//    public function buildTestInfoCollectionData()
//    {
//        return [
//            [
//                __DIR__ . '/../fixtures/allure_results/',
//                __DIR__ . '/../fixtures/results/result.csv',
//            ],
//        ];
//    }
//
//    /**
//     * @dataProvider summTimeByDirectoriesData()
//     *
//     * @param string $inputResultFile
//     * @param string $expectedTimeResultsFile
//     */
//    public function testSummTimeByDirectories(string $inputResultFile, string $expectedTimeResultsFile)
//    {
//        $testInfoItems    = array_map(
//            function (string $string) {
//                $testInfoArray = explode(';', trim($string));
//
//                return new ItemTestInfo($testInfoArray[0], $testInfoArray[1], $testInfoArray[2], (int) $testInfoArray[3]);
//            },
//            file($inputResultFile)
//        );
//        $testDirsWithTime = $this->handler->summTimeByDirectories($testInfoItems);
//        $this->assertEquals($testDirsWithTime, json_decode(file_get_contents($expectedTimeResultsFile), true));
//    }
//
//    public function summTimeByDirectoriesData()
//    {
//        return [
//            [
//                __DIR__ . '/../fixtures/results/result.csv',
//                __DIR__ . '/../fixtures/results/time_results.json',
//            ],
//        ];
//    }
//
//    /**
//     * @dataProvider separateDirectoriesByTimeData()
//     *
//     * @param string $timeResultsFile
//     * @param string $expectedGroupSeparatingFile
//     * @param int $countSuit
//     */
//    public function testSeparateDirectoriesByTime(string $timeResultsFile, string $expectedGroupSeparatingFile, int $countSuit)
//    {
//        $testInfoItems = json_decode(file_get_contents($timeResultsFile), true);
//
//        $arGroups = $this->handler->separateDirectoriesByTime($testInfoItems, $countSuit);
//
//        $this->assertEquals(
//            array_map(
//                function (GroupBlockInfo $testInfo) {
//                    return $testInfo->asArray();
//                },
//                $arGroups
//            ),
//            json_decode(file_get_contents($expectedGroupSeparatingFile), true)
//        );
//    }
//
//    public function separateDirectoriesByTimeData()
//    {
//        return [
//            [
//                __DIR__ . '/../fixtures/results/time_results.json',
//                __DIR__ . '/../fixtures/results/time_results_6_sutecases.json',
//                6,
//            ],
//        ];
//    }
//}
