<?php
declare(strict_types=1);

namespace Tests\Handler;

use PHPUnit\Framework\MockObject\MockObject;
use TestSeparator\Configuration;
use TestSeparator\Exception\NeededCountGroupsAndCountDefaultGroupsNotEqual;
use TestSeparator\Handler\LazyTestSeparatorHandler;
use PHPUnit\Framework\TestCase;
use TestSeparator\Service\FileSystemHelper;
use TestSeparator\Service\Logger;
use TestSeparator\Service\Validator\SeparatedEntityValidator;

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

    /**
     * @var SeparatedEntityValidator
     */
    private $entityValidator;


    /**
     * @var string
     */
    private $testsPath;

    protected function setUp()
    {
        parent::setUp();

        $this->defaultGroupsPath = 'tests/data/default-groups/';
        $this->resultPath = 'tests/data/groups/';
        $this->testsPath = 'tests/';

        /** @var Logger|MockObject $logger */
        $this->logger = $this->createMock(Logger::class);

        /** @var Configuration|MockObject $configuration */
        $this->configuration = $this->createMock(Configuration::class);

        $this->entityValidator = new SeparatedEntityValidator($this->testsPath);

        FileSystemHelper::removeAllFilesInDirectory($this->resultPath);
    }

    protected function tearDown()
    {
        parent::tearDown();
        FileSystemHelper::removeAllFilesInDirectory($this->resultPath);
    }

    public function testSeparateTestsOk()
    {
        $countSuits = 2;

        $this->configuration->expects($this->once())
            ->method('getDefaultGroupsDir')
            ->willReturn($this->defaultGroupsPath);

        $this->configuration->expects($this->once())
            ->method('getResultPath')
            ->willReturn($this->resultPath);

        $this->logger->expects($this->at(0))
            ->method('warning')
            ->with('Entity tests/data/tests/functional/FakeTest_invalid.php:fakeMethodTest in file tests/data/default-groups/time_group_1.txt is invalid.');

        $this->logger->expects($this->at(1))
            ->method('warning')
            ->with('Needed count groups and count result groups not equal. Expected - 2, got - 1.');

        $handler = new LazyTestSeparatorHandler($this->configuration, $this->entityValidator, $this->logger);
        $this->assertNull($handler->separateTests($countSuits));
    }

    public function testSeparateTestsFail()
    {
        $this->expectException(NeededCountGroupsAndCountDefaultGroupsNotEqual::class);
        $this->expectExceptionMessage('Needed count groups and count default groups not equal. Expected - 3, got - 2.');

        $countSuits = 3;

        $this->configuration->expects($this->once())
            ->method('getDefaultGroupsDir')
            ->willReturn($this->defaultGroupsPath);

        $handler = new LazyTestSeparatorHandler($this->configuration, $this->entityValidator, $this->logger);
        $handler->separateTests($countSuits);
    }
}
