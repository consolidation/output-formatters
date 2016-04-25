<?php
namespace Consolidation\OutputFormatters\Formatters;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\ConfigureInterface;
use Consolidation\OutputFormatters\ValidationInterface;
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
class TableFormatter implements FormatterInterface, ConfigureInterface, ValidationInterface
{
    protected $fieldLabels;
    protected $defaultFields;
    protected $tableStyle;

    public function __construct()
    {
        $this->tableStyle = 'default';
    }

    /**
     * @inheritdoc
     */
    public function configure($configurationData)
    {
        if (isset($configurationData['table-style'])) {
            $this->tableStyle = $configurationData['table-style'];
        }
    }

    public function validate($structuredData)
    {
        // If the provided data was of class RowsOfFields
        // or AssociativeList, it will be converted into
        // a TableTransformation object by the restructure call.
        if (!$structuredData instanceof TableDataInterface) {
            throw new IncompatibleDataException(
                $this,
                $structuredData,
                [
                    new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields'),
                    new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\AssociativeList'),
                ]
            );
        }
        return $structuredData;
    }

    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $tableTransformer, $options = [])
    {
        $options += [
            'table-style' => $this->tableStyle,
            'include-field-labels' => true,
        ];

        $table = new Table($output);
        $table->setStyle($options['table-style']);
        $headers = $tableTransformer->getHeaders();
        $isList = $tableTransformer->isList();
        $includeHeaders = $options['include-field-labels'];
        if ($includeHeaders && !$isList && !empty($headers)) {
            $table->setHeaders($headers);
        }
        $table->setRows($tableTransformer->getTableData($includeHeaders && $isList));
        $table->render();
    }
}
