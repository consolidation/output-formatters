<?php
namespace Consolidation\OutputFormatters;

interface RestructureInterface
{
    /**
     * Allow structured data to be restructured -- i.e. to select fields
     * to show, reorder fields, etc.
     *
     * @param mixed $configurationData Annotation data for configuration
     * @param mixed $options Options for restructuring (user selected)
     */
    public function restructure($configurationData, $options);
}
