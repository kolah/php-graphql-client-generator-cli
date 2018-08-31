<?php

namespace Kolah\GraphQL\Command;

use Kolah\GraphQL\Generator\CustomScalarGenerator;
use Kolah\GraphQL\Generator\EnumTypeGenerator;
use Kolah\GraphQL\Generator\FieldSelectionGenerator;
use Kolah\GraphQL\Generator\Generator;
use Kolah\GraphQL\Generator\InputTypeGenerator;
use Kolah\GraphQL\Generator\InterfaceGenerator;
use Kolah\GraphQL\Generator\OutputTypeGenerator;
use Kolah\GraphQL\Generator\ServiceGenerator;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    const OPTION_SCHEMA = 'schema';
    const OPTION_OUTPUT_DIR = 'output-dir';
    const OPTION_TYPE_MAPPING = 'map';
    const OPTION_CLIENT_NAMESPACE = 'namespace';

    const REQUIRED_DIRS = [
        'Types'
    ];

    protected function configure()
    {
        $this->setName('generate');
        $this->setDescription('Generate GraphQL client from schema file');

        $this->addOption(self::OPTION_CLIENT_NAMESPACE, 'ns', InputOption::VALUE_REQUIRED, 'Generated client namespace');
        $this->addOption(self::OPTION_SCHEMA, 's', InputOption::VALUE_REQUIRED, 'Path to GraphQL schema file');
        $this->addOption(self::OPTION_OUTPUT_DIR, 'o', InputOption::VALUE_REQUIRED, 'Target directory');
        $this->addOption(self::OPTION_TYPE_MAPPING, 'm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Allows to replace generated classes with custom implementation. Example: GraphQLTypeName:Namespace\ClassName', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schemaFilename = $input->getOption(self::OPTION_SCHEMA);
        $outputDir = $input->getOption(self::OPTION_OUTPUT_DIR);
        $namespace = $input->getOption(self::OPTION_CLIENT_NAMESPACE);
        if (!$namespace) {
            throw new \RuntimeException('Namespace');
        }

        if (!$schemaFilename) {
            throw new \RuntimeException('Schema path is required');
        }

        if (!$outputDir) {
            throw new \RuntimeException('Output directory is required');
        }

        if (!file_exists($schemaFilename)) {
            throw new \RuntimeException('Schema file not found');
        }
        $mapping = [];
        foreach ($input->getOption(self::OPTION_TYPE_MAPPING) as $map) {
            $item = explode(':', $map);
            if (count($item) != 2) {
                throw new \RuntimeException(sprintf('Incorrect map: %s', $map));
            }
            $mapping[$item[0]] = $item[1];
        }

        FileSystem::createDir($outputDir);
        foreach (self::REQUIRED_DIRS as $requiredDir) {
            FileSystem::createDir(sprintf('%s/%s', $outputDir, $requiredDir));
        }

        $generator = new Generator();
        $generator->addGenerator(new OutputTypeGenerator());
        $generator->addGenerator(new CustomScalarGenerator());
        $generator->addGenerator(new InputTypeGenerator());
        $generator->addGenerator(new EnumTypeGenerator());
        $generator->addGenerator(new InterfaceGenerator());
        $generator->addGenerator(new FieldSelectionGenerator());
        $generator->addGenerator(new ServiceGenerator());

        $generator->build($namespace, $schemaFilename, $outputDir, $mapping);
    }


}