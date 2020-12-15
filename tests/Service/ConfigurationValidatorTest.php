<?php
declare(strict_types=1);

namespace Tests\Service;

use Psr\Log\LoggerInterface;
use Tests\Fixtures\ConfigurationFixture;
use TestSeparator\Configuration;
use TestSeparator\Exception\Strategy\AllDefaultSeparatingStrategiesAreInvalidException;
use TestSeparator\Exception\Strategy\CodeceptionReportsDirIsEmptyException;
use TestSeparator\Exception\DefaultSeparatingStrategiesNotFoundOrEmptyException;
use TestSeparator\Exception\InvalidPathToResultDirectoryException;
use TestSeparator\Exception\InvalidPathToTestsDirectoryException;
use TestSeparator\Exception\NotAvailableDepthLevelException;
use TestSeparator\Exception\Strategy\PathToCodeceptionReportsDirIsEmptyException;
use TestSeparator\Exception\UnknownSeparatingStrategyException;
use TestSeparator\Service\ConfigurationValidator;
use PHPUnit\Framework\TestCase;
use TestSeparator\Service\Logger;
use \PHPUnit\Framework\MockObject\MockObject;

class ConfigurationValidatorTest extends TestCase
{
    /**
     * @var MockObject|LoggerInterface
     */
    protected $logger;

    protected function setUp()
    {
        parent::setUp();
        $this->logger = $this->createMock(Logger::class);
    }


    /**
     * @dataProvider dataValidateOk()
     *
     * @param Configuration $configuration
     */
    public function testValidateOk(Configuration $configuration): void
    {
        $this->assertTrue((new ConfigurationValidator($this->logger))->validate($configuration));
    }

    public function dataValidateOk(): iterable
    {
        yield [
            new Configuration(ConfigurationFixture::getValidConfigurationArray())
        ];
    }

    /**
     * @dataProvider dataValidateFail()
     *
     * @param Configuration $configuration
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     */
    public function testValidateFail(Configuration $configuration, string $expectedExceptionClass, string $expectedExceptionMessage): void
    {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        (new ConfigurationValidator($this->logger))->validate($configuration);
    }

    public function dataValidateFail(): iterable
    {
        $validConfigurationArray = ConfigurationFixture::getValidConfigurationArray();

        $invalidConfigurationArray1 = $validConfigurationArray;
        $invalidConfigurationArray1['level'] = 'unknown';

        yield 'Not available depth level was got.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray1),
            'Expected Exception Class' => NotAvailableDepthLevelException::class,
            'Expected Exception Message' => 'Not available depth level was got.',
        ];

        $invalidConfigurationArray2 = $validConfigurationArray;
        $invalidConfigurationArray2['tests-directory'] = 'invalid_path';

        yield 'Path to tests directory is Invalid.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray2),
            'Expected Exception Class' => InvalidPathToTestsDirectoryException::class,
            'Expected Exception Message' => 'Path to tests directory is Invalid.',
        ];

        $invalidConfigurationArray3 = $validConfigurationArray;
        $invalidConfigurationArray3['result-path'] = 'invalid_path';

        yield 'Path to results directory is Invalid.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray3),
            'Expected Exception Class' => InvalidPathToResultDirectoryException::class,
            'Expected Exception Message' => 'Path to results directory is Invalid.',
        ];

        $invalidConfigurationArray4 = $validConfigurationArray;
        $invalidConfigurationArray4['use-default-separating-strategy'] = false;
        $invalidConfigurationArray4['separating-strategy'] = 'invalid_strategy';

        yield 'There was got unknown separating strategy.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray4),
            'Expected Exception Class' => UnknownSeparatingStrategyException::class,
            'Expected Exception Message' => 'There was got unknown separating strategy.',
        ];

        $invalidConfigurationArray5 = $validConfigurationArray;
        $invalidConfigurationArray5['test-suites-directories'] = [];

        yield 'All Default Separating Strategies are invalid' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray5),
            'Expected Exception Class' => AllDefaultSeparatingStrategiesAreInvalidException::class,
            'Expected Exception Message' => 'All Default Separating Strategies are invalid',
        ];

        $invalidConfigurationArray6 = $validConfigurationArray;
        $invalidConfigurationArray6['separating-strategy'] = 'codeception-report';
        $invalidConfigurationArray6['use-default-separating-strategy'] = false;
        $invalidConfigurationArray6['codeception-reports-directory'] = '';

        yield 'Path to Codeception Reports directory is empty.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray6),
            'Expected Exception Class' => PathToCodeceptionReportsDirIsEmptyException::class,
            'Expected Exception Message' => 'Path to Codeception Reports directory is empty.',
        ];

        $invalidConfigurationArray7 = $validConfigurationArray;
        $invalidConfigurationArray7['separating-strategy'] = 'codeception-report';
        $invalidConfigurationArray7['codeception-reports-directory'] = 'tests/data/empty-reports/';
        $invalidConfigurationArray7['use-default-separating-strategy'] = false;

        yield 'Codeception Reports directory is empty.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray7),
            'Expected Exception Class' => CodeceptionReportsDirIsEmptyException::class,
            'Expected Exception Message' => 'Codeception Reports directory is empty.',
        ];

        $invalidConfigurationArray8 = $validConfigurationArray;
        $invalidConfigurationArray8['default-separating-strategies'] = [];

        yield 'Default separating strategies not found or empty' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray8),
            'Expected Exception Class' => DefaultSeparatingStrategiesNotFoundOrEmptyException::class,
            'Expected Exception Message' => 'Default separating strategies not found or empty',
        ];

        $invalidConfigurationArray9 = $validConfigurationArray;
        $invalidConfigurationArray9['default-separating-strategies'] = [
            'invalid_strategy'
        ];

        yield 'There was got unknown default separating strategy.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray9),
            'Expected Exception Class' => UnknownSeparatingStrategyException::class,
            'Expected Exception Message' => 'There was got unknown default separating strategy.',
        ];
    }

    /**
     * @dataProvider dataValidateLoggerMessage()
     *
     * @param Configuration $configuration
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     * @param string $expectedLogMessage
     */
    public function testValidateLoggerMessage(
        Configuration $configuration,
        string $expectedExceptionClass,
        string $expectedExceptionMessage,
        string $expectedLogMessage
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->logger->expects($this->once())
            ->method('notice')
            ->with($expectedLogMessage);

        (new ConfigurationValidator($this->logger))->validate($configuration);
    }

    public function dataValidateLoggerMessage()
    {
        $invalidConfigurationArray = ConfigurationFixture::getValidConfigurationArray();
        $invalidConfigurationArray['test-suites-directories'] = [];

        yield 'All Default Separating Strategies are invalid' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray),
            'Expected Exception Class' => AllDefaultSeparatingStrategiesAreInvalidException::class,
            'Expected Exception Message' => 'All Default Separating Strategies are invalid',
            'Expected Logger Message' => 'Default strategy method-size is invalid',
        ];
    }

    /**
     * @dataProvider dataValidateWithChangingStrategy()
     *
     * @param Configuration $configuration
     * @param string $expectedStrategy
     */
    public function testValidateWithChangingStrategy(Configuration $configuration, string $expectedStrategy): void
    {
        (new ConfigurationValidator($this->logger))->validate($configuration);

        $this->assertEquals($expectedStrategy, $configuration->getSeparatingStrategy());
    }

    public function dataValidateWithChangingStrategy(): iterable
    {
        $configurationArray = ConfigurationFixture::getValidConfigurationArray();

        $configurationArray1 = $configurationArray;
        $configurationArray1['separating-strategy'] = 'codeception-report';
        $configurationArray1['use-default-separating-strategy'] = true;

        yield 'Check that codeception-report change to method-size' => [
            'Configuration' => new Configuration($configurationArray1),
            'Expected Strategy' => 'method-size',
        ];
    }
}
