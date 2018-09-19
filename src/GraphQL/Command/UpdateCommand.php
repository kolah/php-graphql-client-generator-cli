<?php

namespace Kolah\GraphQL\Command;

use Humbug\SelfUpdate\Strategy\ShaStrategy;
use Symfony\Component\Console\Command\Command;
use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    const PHAR_URL = 'https://kolah.github.io/php-graphql-client-generator-cli/bin/gql2php.phar';
    const VERSION_URL = 'https://kolah.github.io/php-graphql-client-generator-cli/bin/gql2php.phar.version';
    const OPTION_ROLLBACK = 'rollback';

    protected function configure()
    {
        $this->setName('update');
        $this->setDescription('Update generator to the newest version');
        $this->addOption(self::OPTION_ROLLBACK, null, InputOption::VALUE_NONE, 'Attempts to rollback to the previous version');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new Updater();
        if (false !== $input->getOption(self::OPTION_ROLLBACK)) {
            try {
                $result = $updater->rollback();
                if (!$result) {
                    // report failure!
                    $output->writeln('<error>Rollback failed</error>');
                    return 1;
                }
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Rollback failed: %s</error>', trim($e->getMessage())));
                return 1;
            }
            return 0;
        }

        $updater->setStrategy(Updater::STRATEGY_SHA1);
        /** @var ShaStrategy $strategy */
        $strategy = $updater->getStrategy();
        $strategy->setPharUrl(self::PHAR_URL);
        $strategy->setVersionUrl(self::VERSION_URL);
        try {
            $result = $updater->update();
            if (!$result) {
                $output->writeln('<info>Current version is most recent</info>');
                return 0;
            }
            $new = $updater->getNewVersion();
            $old = $updater->getOldVersion();
            $output->writeln(sprintf('<info>Updated from %s to %s</info>', $old, $new));
            return 0;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Update failed: %s</error>', trim($e->getMessage())));
            return 1;
        }
    }
}
