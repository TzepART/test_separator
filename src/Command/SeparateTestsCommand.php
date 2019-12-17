<?php
declare(strict_types=1);

namespace TestSeparator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TestSeparator\Handler\SeparateTestsHandler;

class SeparateTestsCommand extends Command
{
    protected static $defaultName = 'app:separate-tests';

    /**
     * @var SeparateTestsHandler
     */
    private $separateTestsHandler;

    /**
     * SeparateTestsCommand constructor.
     */
    public function __construct(SeparateTestsHandler $separateTestsHandler)
    {
        parent::__construct();
        $this->separateTestsHandler = $separateTestsHandler;
    }


    protected function configure()
    {
        $this
            ->setDescription('Separate Tests on several time groups.')
            ->addArgument('allure_file', InputArgument::REQUIRED, 'The file with preview tests results.')
            ->addArgument('result_path', InputArgument::REQUIRED, 'The directory where results will.')
            ->addArgument('count_group', InputArgument::REQUIRED, 'The count of time groups.')
            ->setHelp('This command allows you to separate tests...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln('Separating started...');

        $suitesFile   = $input->getArgument('allure_file');
        $groupDirPath = (int) $input->getArgument('result_path');
        $countSuit    = (int) $input->getArgument('count_group');


        $output->writeln('Your test are separated!');

        return 0;
    }
}
