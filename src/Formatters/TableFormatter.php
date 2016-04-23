<?php
namespace Consolidation\OutputFormatters\Formatters;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\ConfigureInterface;
use Consolidation\OutputFormatters\ValidationInterface;
use Consolidation\OutputFormatters\Transformations\TableTransformation;
use Consolidation\OutputFormatters\Transformations\PropertyParser;
use Consolidation\OutputFormatters\Transformations\ReorderFields;

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
        // If the returned data is of class RowsOfFields, that will
        // be converted into a TableTransformation object.
        if (!$structuredData instanceof TableTransformation) {
            // TODO: Define our own Exception class
            throw new \Exception("Data provided to table formatter must be an instance of RowsOfFields. Instead, a " . get_class($structuredData) . " was provided.", 1);
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
        if ($options['include-field-labels']) {
            $table->setHeaders($tableTransformer->getHeaders());
        }
        $table->setRows($tableTransformer->getData());
        $table->render();
    }
}
