<?php
declare(strict_types=1);

namespace TestSeparator\Service;

use TestSeparator\Configuration;

class ConfigurationValidator
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * ConfigurationValidator constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function validate(): void
    {

    }
}
