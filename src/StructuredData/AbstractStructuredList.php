<?php
namespace Consolidation\OutputFormatters\StructuredData;

use Consolidation\OutputFormatters\StructuredData\RestructureInterface;
use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\ListDataInterface;
use Consolidation\OutputFormatters\Transformations\ReorderFields;
use Consolidation\OutputFormatters\Transformations\TableTransformation;

/**
 * Holds an array where each element of the array is one row,
 * and each row contains an associative array where the keys
 * are the field names, and the values are the field data.
 *
 * It is presumed that every row contains the same keys.
 */
abstract class AbstractStructuredList extends \ArrayObject implements RestructureInterface, ListDataInterface
{
    protected $data;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    abstract public function restructure(FormatterOptions $options);

    abstract public function getListData();

    protected function createTableTransformation($data, $options)
    {
        $defaults = $this->defaultOptions();

        $reorderer = new ReorderFields();
        $fieldLabels = $reorderer->reorder(
            $options->get(
                FormatterOptions::FIELDS,
                [
                    FormatterOptions::FIELDS => [$options->get(FormatterOptions::FIELD)]
                ] + $defaults
            ),
            $options->get(
                FormatterOptions::FIELD_LABELS, $defaults
            ),
            $data
        );

        $tableTransformer = new TableTransformation($data, $fieldLabels, $options->get(FormatterOptions::ROW_LABELS, $defaults));
        if ($options->get(FormatterOptions::LIST_ORIENTATION, $defaults)) {
            $tableTransformer->setLayout(TableTransformation::LIST_LAYOUT);
        }

        return $tableTransformer;
    }

    /**
     * A structured list may provide its own set of default options. These
     * will be used in place of the command's default options (from the
     * annotations) in instances where the user does not provide the options
     * explicitly (on the commandline) or implicitly (via a configuration file).
     *
     * @return array
     */
    protected function defaultOptions()
    {
        return [
            FormatterOptions::FIELDS => [],
            FormatterOptions::FIELD_LABELS => [],
            FormatterOptions::ROW_LABELS => [],
            FormatterOptions::DEFAULT_FIELDS => [],
        ];
    }
}
