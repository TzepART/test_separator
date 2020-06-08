<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use TestSeparator\Configuration;
use TestSeparator\Exception\ConfigurationFileDoesNotExist;
use TestSeparator\Exception\ErrorWhileParsingConfigurationFile;
use TestSeparator\Service\ConfigurationValidator;

class ConfigurationFactory
{
    public static function makeConfiguration(string $configPath): Configuration
    {
        $config = self::configurationFileValidate($configPath);

        $configuration = new Configuration($config);

        (new ConfigurationValidator($configuration))->validate();

        return $configuration;
    }

    /**
     * @param string $configPath
     *
     * @return array
     *
     * @throws ConfigurationFileDoesNotExist|ErrorWhileParsingConfigurationFile
     */
    private static function configurationFileValidate(string $configPath): array
    {
        if (!file_exists($configPath)) {
            throw new ConfigurationFileDoesNotExist(ConfigurationFileDoesNotExist::MESSAGE);
        }

        $config = json_decode(file_get_contents($configPath), true);

        if ($config === null) {
            $errorMessage = json_last_error_msg();
            throw new ErrorWhileParsingConfigurationFile(sprintf(ErrorWhileParsingConfigurationFile::MESSAGE_TEMPLATE, $errorMessage));
        }

        return $config;
    }
}
