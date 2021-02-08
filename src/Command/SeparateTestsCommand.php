<?php
declare(strict_types=1);

namespace TestSeparator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TestSeparator\Configuration;
use TestSeparator\Handler\TestsSeparatorHandler;
use TestSeparator\Handler\TestsSeparatorInterface;

/**
 * Class SeparateTestsCommand
 * @package TestSeparator\Command
 */
class SeparateTestsCommand extends Command
{
    /**
     * @var TestsSeparatorHandler
     */
    private $separateTestsHandler;

    /**
     * SeparateTestsCommand constructor.
     *
     * @param TestsSeparatorInterface $separateTestsHandler
     * @param string|null $name
     */
    public function __construct(TestsSeparatorInterface $separateTestsHandler, string $name = null)
    {
        parent::__construct($name);
        $this->separateTestsHandler = $separateTestsHandler;
    }


    protected function configure()
    {
        $this
            ->setDescription('Separate Tests on several time groups.')
            ->addArgument('count_group', InputArgument::REQUIRED, 'The count of time groups.')
            ->addOption(Configuration::CODECEPTION_REPORTS_DIRECTORY_KEY, null, InputArgument::OPTIONAL, '')
            ->addOption(Configuration::RESULT_PATH_KEY, null, InputArgument::OPTIONAL, '')
            ->addOption(Configuration::SEPARATING_STRATEGY_KEY, null, InputArgument::OPTIONAL, '')
            ->setHelp('This command allows you to separate tests...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln('Separating started...');

        $countSuit = (int) $input->getArgument('count_group');

        $this->separateTestsHandler->separateTests($countSuit);

        $output->writeln('Your tests are separated!');

        return 0;
    }
}
