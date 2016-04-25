<?php
namespace Consolidation\OutputFormatters\Formatters;

use Symfony\Component\Console\Output\OutputInterface;
use Consolidation\OutputFormatters\FormatterInterface;
use Consolidation\OutputFormatters\OverrideRestructureInterface;
use Consolidation\OutputFormatters\StructuredData\ListDataInterface;

/**
 * Display the data in a simple list.
 *
 * This formatter prints a plain, unadorned list of data,
 * with each data item appearing on a separate line.  If you
 * wish your list to contain headers, then use the table
 * formatter, and wrap your data in an AssociativeList.
 */
class ListFormatter implements FormatterInterface, OverrideRestructureInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, $options = [])
    {
        $output->writeln(implode("\n", $data));
    }

    /**
     * @inheritdoc
     */
    public function overrideRestructure($structuredOutput, $configurationData, $options)
    {
        // If the structured data implements ListDataInterface,
        // then we will render whatever data its 'getListData'
        // method provides.
        if ($structuredOutput instanceof ListDataInterface) {
            return $structuredOutput->getListData();
        }
    }
}
