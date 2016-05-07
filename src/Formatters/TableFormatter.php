<?php
namespace Consolidation\OutputFormatters\Formatters;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\ValidationInterface;
use Consolidation\OutputFormatters\FormatterOptions;
use Consolidation\OutputFormatters\StructuredData\TableDataInterface;
use Consolidation\OutputFormatters\Transformations\ReorderFields;
use Consolidation\OutputFormatters\Exception\IncompatibleDataException;

/**
 * Display a table of data with the Symfony Table class.
 *
 * This formatter takes data of either the RowsOfFields or
 * AssociativeList data type.  Tables can be rendered with the
 * rows running either vertically (the normal orientation) or
 * horizontally.  By default, associative lists will be displayed
 * as two columns, with the key in the first column and the
 * value in the second column.
 */
class TableFormatter implements FormatterInterface, ValidationInterface, RenderDataInterface
{
    use RenderTableDataTrait;

    protected $fieldLabels;
    protected $defaultFields;

    public function __construct()
    {
    }

    public function validDataTypes()
    {
        return
            [
                new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields'),
                new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\AssociativeList')
            ];
    }

    /**
     * @inheritdoc
     */
    public function validate($structuredData)
    {
        // If the provided data was of class RowsOfFields
        // or AssociativeList, it will be converted into
        // a TableTransformation object by the restructure call.
        if (!$structuredData instanceof TableDataInterface) {
            throw new IncompatibleDataException(
                $this,
                $structuredData,
                $this->validDataTypes()
            );
        }
        return $structuredData;
    }


    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $tableTransformer, FormatterOptions $options)
    {
        $defaults = [
            FormatterOptions::TABLE_STYLE => 'consolidation',
            FormatterOptions::INCLUDE_FIELD_LABELS => true,
        ];

        $table = new Table($output);

        // The 'consolidation' style is the same as the 'symfony-style-guide'
        // style, except it maintains the colored headers used in 'default'.
        $consolidationStyle = new TableStyle();
        $consolidationStyle
            ->setHorizontalBorderChar('-')
            ->setVerticalBorderChar(' ')
            ->setCrossingChar(' ')
        ;
        $table->setStyleDefinition('consolidation', $consolidationStyle);

        $table->setStyle($options->get(FormatterOptions::TABLE_STYLE, $defaults));
        $headers = $tableTransformer->getHeaders();
        $isList = $tableTransformer->isList();
        $includeHeaders = $options->get(FormatterOptions::INCLUDE_FIELD_LABELS, $defaults);
        if ($includeHeaders && !$isList && !empty($headers)) {
            $table->setHeaders($headers);
        }
        $table->setRows($tableTransformer->getTableData($includeHeaders && $isList));
        $table->render();
    }
}
