<?php
declare(strict_types=1);

namespace Tests\Handler;

use PHPUnit\Framework\MockObject\MockObject;
use TestSeparator\Configuration;
use TestSeparator\Exception\NeededCountGroupsAndCountDefaultGroupsNotEqual;
use TestSeparator\Exception\NeededCountGroupsAndCountResultGroupsNotEqual;
use TestSeparator\Handler\LazyTestSeparatorHandler;
use PHPUnit\Framework\TestCase;
use TestSeparator\Service\FileSystemHelper;
use TestSeparator\Service\Logger;

class LazyTestSeparatorHandlerTest extends TestCase
{
    /**
     * @var string
     */
    protected $defaultGroupsPath;

    /**
     * @var string
     */
    protected $resultPath;

    /**
     * @var Logger|MockObject
     */
    protected $logger;

    /**
     * @var Configuration|MockObject
     */
    protected $configuration;

    protected function setUp()
    {
        parent::setUp();

        $this->defaultGroupsPath = 'tests/data/default-groups/';
        $this->resultPath = 'tests/data/groups/';

        /** @var Logger|MockObject $logger */
        $this->logger = $this->createMock(Logger::class);

        /** @var Configuration|MockObject $configuration */
        $this->configuration = $this->createMock(Configuration::class);

        FileSystemHelper::removeAllFilesInDirectory($this->resultPath);
    }

    protected function tearDown()
    {
        parent::tearDown();
        FileSystemHelper::removeAllFilesInDirectory($this->resultPath);
    }

    public function testSeparateTestsOk()
    {
        $countSuits = 1;

        $this->configuration->expects($this->exactly(2))
            ->method('getDefaultGroupsDir')
            ->willReturn($this->defaultGroupsPath);

        $this->configuration->expects($this->exactly(2))
            ->method('getResultPath')
            ->willReturn($this->resultPath);

        $handler = new LazyTestSeparatorHandler($this->configuration, $this->logger);
        $this->assertNull($handler->separateTests($countSuits));
    }

    public function testSeparateTestsFail()
    {
        $this->expectException(NeededCountGroupsAndCountDefaultGroupsNotEqual::class);
        $this->expectExceptionMessage('Needed count groups and count default groups not equal. Expected - 2, got - 1.');

        $countSuits = 2;

        $this->configuration->expects($this->once())
            ->method('getDefaultGroupsDir')
            ->willReturn($this->defaultGroupsPath);

        $handler = new LazyTestSeparatorHandler($this->configuration, $this->logger);
        $handler->separateTests($countSuits);
    }

    public function testSeparateTestsFailAfterCopy()
    {
        $countSuits = 1;
        file_put_contents($this->resultPath.'fake-file.txt', 'fake');

        $this->expectException(NeededCountGroupsAndCountResultGroupsNotEqual::class);
        $this->expectExceptionMessage('Needed count groups and count result groups not equal. Expected - 1, got - 2.');

        $this->configuration->expects($this->exactly(2))
            ->method('getDefaultGroupsDir')
            ->willReturn($this->defaultGroupsPath);

        $this->configuration->expects($this->exactly(2))
            ->method('getResultPath')
            ->willReturn($this->resultPath);

        $handler = new LazyTestSeparatorHandler($this->configuration, $this->logger);
        $handler->separateTests($countSuits);
    }

}
