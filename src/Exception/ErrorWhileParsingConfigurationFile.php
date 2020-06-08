<?php
declare(strict_types=1);

namespace TestSeparator\Exception;

class ErrorWhileParsingConfigurationFile extends ValidationOfConfigurationException
{
    public const MESSAGE_TEMPLATE = 'Error while parsing configuration file. Error - %s';
}
