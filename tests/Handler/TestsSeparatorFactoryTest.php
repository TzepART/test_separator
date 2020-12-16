<?php
declare(strict_types=1);

namespace Tests\Handler;

use TestSeparator\Configuration;
use TestSeparator\Handler\LazyTestSeparatorHandler;
use TestSeparator\Handler\TestsSeparatorFactory;
use PHPUnit\Framework\TestCase;
use TestSeparator\Handler\TestsSeparatorHandler;
use TestSeparator\Service\Logger;

class TestsSeparatorFactoryTest extends TestCase
{
    public function testMakeTestsSeparatorHandler(): void
    {
        /** @var Logger $logger */
        $logger = $this->createMock(Logger::class);

        /** @var Configuration $configuration */
        $configuration = $this->createMock(Configuration::class);
        $configuration->expects($this->once())
            ->method('getResultPath')
            ->willReturn('');
        $configuration->expects($this->once())
            ->method('getTestSuitesDirectories')
            ->willReturn([]);
        $configuration->expects($this->exactly(2))
            ->method('getTestsDirectory')
            ->willReturn('tests/');
        $configuration->expects($this->exactly(3))
            ->method('getSeparatingStrategy')
            ->willReturn('method-size');
        $configuration->expects($this->once())
            ->method('getDepthLevel')
            ->willReturn('class');

        $this->assertEquals(TestsSeparatorHandler::class, get_class(TestsSeparatorFactory::makeTestsSeparator($configuration, $logger)));
    }

    public function testMakeLazyTestsSeparatorHandler(): void
    {
        /** @var Logger $logger */
        $logger = $this->createMock(Logger::class);

        /** @var Configuration $configuration */
        $configuration = $this->createMock(Configuration::class);
        $configuration->expects($this->once())
            ->method('getSeparatingStrategy')
            ->willReturn('default-groups');

        $this->assertEquals(LazyTestSeparatorHandler::class, get_class(TestsSeparatorFactory::makeTestsSeparator($configuration, $logger)));
    }
}
