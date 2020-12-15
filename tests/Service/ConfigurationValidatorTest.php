<?php
declare(strict_types=1);

namespace Tests\Service;

use Tests\Fixtures\ConfigurationFixture;
use TestSeparator\Configuration;
use TestSeparator\Exception\CodeceptionReportsDirIsEmptyException;
use TestSeparator\Exception\InvalidPathToResultDirectoryException;
use TestSeparator\Exception\InvalidPathToTestsDirectoryException;
use TestSeparator\Exception\NotAvailableDepthLevelException;
use TestSeparator\Exception\PathToCodeceptionReportsDirIsEmptyException;
use TestSeparator\Exception\SuitesDirectoriesCollectionIsEmptyException;
use TestSeparator\Exception\UnknownSeparatingStrategyException;
use TestSeparator\Service\ConfigurationValidator;
use PHPUnit\Framework\TestCase;

class ConfigurationValidatorTest extends TestCase
{

    /**
     * @dataProvider dataValidateOk()
     *
     * @param Configuration $configuration
     */
    public function testValidateOk(Configuration $configuration): void
    {
        $this->assertTrue((new ConfigurationValidator())->validate($configuration));
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

        (new ConfigurationValidator())->validate($configuration);
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

        yield 'Tests suites directories Collection is empty.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray5),
            'Expected Exception Class' => SuitesDirectoriesCollectionIsEmptyException::class,
            'Expected Exception Message' => 'Tests suites directories Collection is empty.',
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
        $invalidConfigurationArray6['codeception-reports-directory'] = 'tests/data/empty-reports/';
        $invalidConfigurationArray7['use-default-separating-strategy'] = false;

        yield 'Codeception Reports directory is empty.' => [
            'invalid Configuration' => new Configuration($invalidConfigurationArray7),
            'Expected Exception Class' => CodeceptionReportsDirIsEmptyException::class,
            'Expected Exception Message' => 'Codeception Reports directory is empty.',
        ];
    }
}
