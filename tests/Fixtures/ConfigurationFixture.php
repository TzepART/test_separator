<?php
declare(strict_types=1);

namespace Tests\Fixtures;

class ConfigurationFixture
{
    public static function getValidConfigurationArray(): array
    {
        return [
            'separating-strategy' => 'codeception-report',
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
            ]
        ];
    }

    public static function getDummyValidConfigurationArray(): array
    {
        return [
            'separating-strategy' => 'codeception-report',
            'use-default-separating-strategy' => true,
            'codeception-reports-directory' => '/path/to/file/with/codeception/reports/',
            'tests-directory' => '/path/to/project/tests/',
            'result-path' => '/path/to/project/file/groups/',
            'level' => 'method',
            'test-suites-directories' => [
                'list',
                'sub-directories',
                'with',
                'test-suites',
            ],
            'default-separating-strategies' => [
                'method-size',
            ]
        ];
    }
}