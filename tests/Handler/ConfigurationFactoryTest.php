<?php
declare(strict_types=1);

namespace Tests\Handler;

use Symfony\Component\Yaml\Exception\ParseException;
use TestSeparator\Exception\ConfigurationFileDoesNotExist;
use TestSeparator\Exception\ErrorWhileParsingConfigurationFile;
use TestSeparator\Handler\ConfigurationFactory;
use PHPUnit\Framework\TestCase;
use TestSeparator\Service\ConfigurationValidator;

class ConfigurationFactoryTest extends TestCase
{
    /**
     * @dataProvider dataMakeConfigurationArrayByFileOk()
     *
     * @param string $validConfigPath
     * @param array $expectedArrayConfig
     */
    public function testMakeConfigurationArrayByFileOk(string $validConfigPath, array $expectedArrayConfig): void
    {
        $this->assertEquals($expectedArrayConfig, ConfigurationFactory::makeConfigurationArrayByFile($validConfigPath));
    }

    public function dataMakeConfigurationArrayByFileOk(): iterable
    {
        yield [
            'Config file path' => 'tests/data/config/valid_config.yml',
            'Expected config array' => [
                'separating-strategy' => 'codeception-report',
                'use-default-separating-strategy' => false,
                'codeception-reports-directory' => '/path/to/file/with/codeception/test/',
                'tests-directory' => '/path/to/project/tests/',
                'result-path' => '/path/to/project/file/groups/',
                'level' => 'method',
                'test-suites-directories' => [
                    'list',
                    'sub-directories',
                    'with',
                    'test-suites',

                ],
            ]
        ];
    }

    /**
     * @dataProvider dataMakeConfigurationArrayByFileInvalid()
     *
     * @param string $configPath
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     */
    public function testMakeConfigurationArrayByFileInvalid(string $configPath, string $expectedExceptionClass, string $expectedExceptionMessage): void
    {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        ConfigurationFactory::makeConfigurationArrayByFile($configPath);
    }

    public function dataMakeConfigurationArrayByFileInvalid(): iterable
    {
        yield 'Empty config file' => [
            'Config file path' => 'tests/data/config/invalid_config_empty.yml',
            'Expected Exception Class' => ErrorWhileParsingConfigurationFile::class,
            'Expected Exception Message' => ErrorWhileParsingConfigurationFile::MESSAGE,
        ];

        yield 'Invalid Yaml file' => [
            'Config file path' => 'tests/data/config/invalid_config_yaml_problems.yml',
            'Expected Exception Class' => ParseException::class,
            'Expected Exception Message' => 'Mapping values are not allowed in multi-line blocks at line 2 (near "separating-strategy \'codeception-report\'")',
        ];

        yield 'Not exists config file' => [
            'Config file path' => 'tests/data/config/fake_config_yaml.yml',
            'Expected Exception Class' => ConfigurationFileDoesNotExist::class,
            'Expected Exception Message' => ConfigurationFileDoesNotExist::MESSAGE,
        ];
    }

    /**
     * @dataProvider dataMakeConfigurationOk()
     *
     * @param array $configArray
     * @param array $expectedFieldValues
     */
    public function testMakeConfigurationOk(array $configArray, array $expectedFieldValues): void
    {
        /** @var ConfigurationValidator $configurationValidator */
        $configurationValidator = $this->createMock(ConfigurationValidator::class);
        $configurationValidator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $configuration = ConfigurationFactory::makeConfiguration($configArray, $configurationValidator);

        $this->assertEquals($expectedFieldValues['separatingStrategy'], $configuration->getSeparatingStrategy());
        $this->assertEquals($expectedFieldValues['useDefaultSeparatingStrategy'], $configuration->isUseDefaultSeparatingStrategy());
        $this->assertEquals($expectedFieldValues['codeceptionReportsDir'], $configuration->getCodeceptionReportsDir());
        $this->assertEquals($expectedFieldValues['testsDirectory'], $configuration->getTestsDirectory());
        $this->assertEquals($expectedFieldValues['resultPath'], $configuration->getResultPath());
        $this->assertEquals($expectedFieldValues['depthLevel'], $configuration->getDepthLevel());
        $this->assertEquals($expectedFieldValues['testSuitesDirectories'], $configuration->getTestSuitesDirectories());
    }

    public function dataMakeConfigurationOk(): iterable
    {
        yield [
            'Initial config values' => [
                'separating-strategy' => 'codeception-report',
                'use-default-separating-strategy' => true,
                'codeception-reports-directory' => '/tests-separator-data/reports/',
                'tests-directory' => '/tests-separator-data/tests/',
                'result-path' => '/tests-separator-data/groups/',
                'level' => 'method',
                'test-suites-directories' => [
                    'one',
                    'two',
                ],
            ],
            'Configuration field values' => [
                'separatingStrategy' => 'codeception-report',
                'useDefaultSeparatingStrategy' => true,
                'codeceptionReportsDir' => '/tests-separator-data/reports/',
                'testsDirectory' => '/tests-separator-data/tests/',
                'resultPath' => '/tests-separator-data/groups/',
                'depthLevel' => 'method',
                'testSuitesDirectories' => [
                    'one',
                    'two',
                ],
            ]
        ];
    }
}
