<?php
namespace Consolidation\OutputFormatters;

interface OverrideOptionsInterface
{
    /**
     * Allow the formatter to mess with the configuration options before any
     * transformations et. al. get underway.
     *
     * @param mixed $structuredOutput Data to restructure
     * @param FormatterOptions $options Formatting options
     * @return FormatterOptions
     */
    public function overrideOptions($structuredOutput, FormatterOptions $options);
}
