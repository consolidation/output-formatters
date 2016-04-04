<?php
namespace Consolidation\OutputFormatters;

interface ConfigurationAwareInterface
{
    /**
     * Provide formatter with annotation data to use
     * for configuration.
     *
     * @param mixed $configurationData Annotation data for configuration
     */
    public function configure($configurationData);
}
