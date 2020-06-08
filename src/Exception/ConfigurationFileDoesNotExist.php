<?php
declare(strict_types=1);

namespace TestSeparator\Exception;

class ConfigurationFileDoesNotExist extends ValidationOfConfigurationException
{
    public const MESSAGE = 'Configuration file does not Exist';
}
