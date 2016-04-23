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
use Consolidation\OutputFormatters\Exception\IncompatibleDataException;

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
        // If the provided data was of class RowsOfFields, it will be
        // converted into a TableTransformation object.
        if (!$structuredData instanceof TableTransformation) {
            throw new IncompatibleDataException($this, $structuredData, new \ReflectionClass('\Consolidation\OutputFormatters\StructuredData\RowsOfFields'));
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
