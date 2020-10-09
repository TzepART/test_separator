<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

use Symfony\Component\Yaml\Yaml;
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

        $config = Yaml::parse(file_get_contents($configPath));

        if (!isset($config['test_separator']) || !is_array($config['test_separator'])) {
            throw new ErrorWhileParsingConfigurationFile(ErrorWhileParsingConfigurationFile::MESSAGE);
        }

        return $config['test_separator'];
    }
}
