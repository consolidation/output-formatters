<?php
namespace Consolidation\OutputFormatters;

use Consolidation\OutputFormatters\FormatterInterface;

interface OverrideRestructureInterface
{
    /**
     * Select data to use directly from the structured output,
     * before the restructure operation has been executed.
     *
     * @param mixed $configurationData Configuration data
     * @param mixed $options User options
     * @return mixed
     */
    public function overrideRestructure($structuredOutput, $configurationData, $options);
}
