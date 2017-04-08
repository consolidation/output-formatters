<?php
namespace Consolidation\OutputFormatters\Formatters;

use Consolidation\OutputFormatters\Options\FormatterOptions;
use Consolidation\OutputFormatters\Validate\ValidDataTypesInterface;
use Consolidation\OutputFormatters\Validate\ValidDataTypesTrait;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;

/**
 * Format an array into CLI help string.
 */
class HelpFormatter implements FormatterInterface
{

  /**
   * @inheritdoc
   */
  public function write(OutputInterface $output, $data, FormatterOptions $options)
  {
    $table = new Table($this->output());
    $table->setStyle('compact');

    // @todo. Get input data as an array.
    $output->writeln($command->getDescription());

    if ($usages = $command->getExampleUsages()) {
      $table->addRow(['','']);
      $table->addRow([new TableCell('Examples:', array('colspan' => 2))]);
      foreach ($usages as $key => $description) {
        $table->addRow(['  ' . $key, $description]);
      }
    }

    if ($arguments = $def->getArguments()) {
      $table->addRow(['','']);
      $table->addRow([new TableCell('Arguments:', array('colspan' => 2))]);
      foreach ($arguments as $argument) {
        $formatted = $this->formatArgumentName($argument);
        $description = $argument->getDescription();
        if ($argument->getDefault()) {
          $description .= ' [default: ' . $argument->getDefault() . ']';
        }
        $table->addRow(['  ' . $formatted, $description]);
      }
    }

    if ($options = $def->getOptions()) {
      $table->addRow(['','']);
      $table->addRow([new TableCell('Options:', array('colspan' => 2))]);
      foreach ($options as $option) {
        $formatted = $this->formatOption($option);
        $table->addRow(['  ' . $formatted, $option->getDescription()]);
      }
    }

    if ($topics = $command->getTopics()) {
      $table->addRow(['','']);
      $table->addRow([new TableCell('Topics:', array('colspan' => 2))]);
      foreach ($topics as $topic) {
        // @todo deal with long descriptions
        $table->addRow(['  ' . $topic, substr($allTopics[$topic]['description'], 0, 30)]);
      }
    }

    if ($aliases = $command->getAliases()) {
      $table->addRow(['','']);
      $table->addRow([new TableCell('Aliases: '. implode(' ', $aliases), array('colspan' => 2))]);
    }

    $table->render();

    $output->writeln($help);
  }
}
