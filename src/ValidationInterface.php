<?php
namespace Consolidation\OutputFormatters;

interface ValidationInterface
{
    /**
     * Provide formatter with annotation data to use
     * for configuration.
     *
     * @param mixed $structuredData Data to validate
     *
     * @return mixed
     */
    public function validate($structuredData);
}
