<?php
declare(strict_types=1);

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\ConfigurationFixture;
use TestSeparator\Service\RewriteConfigurationService;

class RewriteConfigurationServiceTest extends TestCase
{
    /**
     * @dataProvider dataGetRewritingOption
     */
    public function testGetRewritingOption(array $initialConfig, array $consoleArguments, array $expectedConfig): void
    {
        $this->assertEquals($expectedConfig, (new RewriteConfigurationService($consoleArguments))->updateRewritingOption($initialConfig));
    }

    public function dataGetRewritingOption(): iterable
    {
        yield 'Without configuration rewriting' => [
            'initial Configuration' => ConfigurationFixture::getValidConfigurationArray(),
            'Console arguments' => [
                "./separate-tests",
                "separate",
                "9",
            ],
            'Result configuration' => ConfigurationFixture::getValidConfigurationArray(),
        ];

        yield 'With codeception-reports-directory rewriting' => [
            'initial Configuration' => ConfigurationFixture::getValidConfigurationArray(),
            'Console arguments' => [
                "./separate-tests",
                "separate",
                "9",
                "--codeception-reports-directory=new/codeception/reports/directory/",
            ],
            'Result configuration' => [
                'separating-strategy' => 'codeception-report',
                'use-default-separating-strategy' => true,
                'codeception-reports-directory' => 'new/codeception/reports/directory/',
                'tests-directory' => 'tests/data/tests/',
                'result-path' => 'tests/data/groups/',
                'level' => 'method',
                'test-suites-directories' => [
                    'functional',
                    'unit',
                ],
                'default-separating-strategies' => [
                    'method-size',
                ],
                'default-groups-directory' => '',
            ],
        ];

        yield 'With result-path rewriting' => [
            'initial Configuration' => ConfigurationFixture::getValidConfigurationArray(),
            'Console arguments' => [
                "./separate-tests",
                "separate",
                "9",
                "--result-path=new/result/path/directory/",
            ],
            'Result configuration' => [
                'separating-strategy' => 'codeception-report',
                'use-default-separating-strategy' => true,
                'codeception-reports-directory' => 'tests/data/reports/',
                'tests-directory' => 'tests/data/tests/',
                'result-path' => 'new/result/path/directory/',
                'level' => 'method',
                'test-suites-directories' => [
                    'functional',
                    'unit',
                ],
                'default-separating-strategies' => [
                    'method-size',
                ],
                'default-groups-directory' => '',
            ],
        ];


        yield 'With separating-strategy rewriting' => [
            'initial Configuration' => ConfigurationFixture::getValidConfigurationArray(),
            'Console arguments' => [
                "./separate-tests",
                "separate",
                "9",
                "--separating-strategy=new-separating-strategy",
            ],
            'Result configuration' => [
                'separating-strategy' => 'new-separating-strategy',
                'use-default-separating-strategy' => true,
                'codeception-reports-directory' => 'tests/data/reports/',
                'tests-directory' => 'tests/data/tests/',
                'result-path' => 'tests/data/groups/',
                'level' => 'method',
                'test-suites-directories' => [
                    'functional',
                    'unit',
                ],
                'default-separating-strategies' => [
                    'method-size',
                ],
                'default-groups-directory' => '',
            ],
        ];

        yield 'With all params rewriting' => [
            'initial Configuration' => ConfigurationFixture::getValidConfigurationArray(),
            'Console arguments' => [
                "./separate-tests",
                "separate",
                "9",
                "--codeception-reports-directory=new/codeception/reports/directory/",
                "--result-path=new/result/path/directory/",
                "--separating-strategy=new-separating-strategy",
            ],
            'Result configuration' => [
                'separating-strategy' => 'new-separating-strategy',
                'use-default-separating-strategy' => true,
                'codeception-reports-directory' => 'new/codeception/reports/directory/',
                'tests-directory' => 'tests/data/tests/',
                'result-path' => 'new/result/path/directory/',
                'level' => 'method',
                'test-suites-directories' => [
                    'functional',
                    'unit',
                ],
                'default-separating-strategies' => [
                    'method-size',
                ],
                'default-groups-directory' => '',
            ],
        ];

        yield 'Checking that params don\'t rewrite if it\'s not available' => [
            'initial Configuration' => ConfigurationFixture::getValidConfigurationArray(),
            'Console arguments' => [
                "./separate-tests",
                "separate",
                "9",
                "--tests-directory=new/tests/directory/",
            ],
            'Result configuration' => ConfigurationFixture::getValidConfigurationArray(),
        ];
    }
}
