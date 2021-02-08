<?php
declare(strict_types=1);

namespace TestSeparator\Service;

use TestSeparator\Configuration;

class RewriteConfigurationService
{
    private const AVAILABLE_REWRITING_OPTION = [
        Configuration::RESULT_PATH_KEY,
        Configuration::CODECEPTION_REPORTS_DIRECTORY_KEY,
        Configuration::SEPARATING_STRATEGY_KEY,
    ];

    private $consoleArguments;

    public function __construct(array $consoleArguments)
    {
        $this->consoleArguments = $consoleArguments;
    }

    public function updateRewritingOption(array $initialArray): array
    {
        $rewritingArguments = [];
        foreach ($this->consoleArguments as $consoleArgument) {
            preg_match('/^--([a-z-]+)=(.+)$/', (string) $consoleArgument, $matches);
            if (isset($matches[1]) && isset($matches[2]) && in_array($matches[1], self::AVAILABLE_REWRITING_OPTION)) {
                $rewritingArguments[$matches[1]] = $matches[2];
            }
        }

        return array_merge($initialArray, $rewritingArguments);
    }
}