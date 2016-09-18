<?php
namespace Consolidation\OutputFormatters;

interface ValidationInterface
{
    /**
     * Return the list of data types acceptable to this formatter
     *
     * TODO: Probably better to turn this around, and just have a
     * method that asks 'is the provided data type acceptable?'
     * @see FormatterManager::isUniversalFormat()
     */
    public function validDataTypes();

    /**
     * Throw an IncompatibleDataException if the provided data cannot
     * be processed by this formatter.  Return the source data if it
     * is valid. The data may be encapsulated or converted if necessary.
     *
     * @param mixed $structuredData Data to validate
     *
     * @return mixed
     */
    public function validate($structuredData);
}
