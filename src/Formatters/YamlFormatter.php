<?php
namespace Consolidation\OutputFormatters\Formatters;

use Symfony\Component\Yaml\Dumper;
use Consolidation\OutputFormatters\FormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class YamlFormatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function write(OutputInterface $output, $data, $options = [])
    {
        $dumper = new Dumper();
        // Set Yaml\Dumper's default indentation for nested nodes/collections to
        // 2 spaces for consistency with Drupal coding standards.
        $dumper->setIndentation(2);
        // The level where you switch to inline YAML is set to PHP_INT_MAX to
        // ensure this does not occur.
        $output->writeln($dumper->dump($data, PHP_INT_MAX, null, null, true));
    }
}
