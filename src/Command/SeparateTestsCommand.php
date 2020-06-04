<?php
declare(strict_types=1);

namespace TestSeparator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TestSeparator\Handler\SeparateTestsHandler;
use TestSeparator\Model\GroupBlockInfo;

/**
 * Class SeparateTestsCommand
 * @package TestSeparator\Command
 */
class SeparateTestsCommand extends Command
{
    /**
     * @var string
     */
    private $groupPrefix = 'time_group_';

    /**
     * @var SeparateTestsHandler
     */
    private $separateTestsHandler;

    /**
     * SeparateTestsCommand constructor.
     *
     * @param SeparateTestsHandler $separateTestsHandler
     * @param string|null $name
     */
    public function __construct(SeparateTestsHandler $separateTestsHandler, string $name = null)
    {
        parent::__construct($name);
        $this->separateTestsHandler = $separateTestsHandler;
    }


    protected function configure()
    {
        $this
            ->setDescription('Separate Tests on several time groups.')
            ->addArgument('count_group', InputArgument::REQUIRED, 'The count of time groups.')
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

        $this->separateTests($countSuit);

        $output->writeln('Your test are separated!');

        return 0;
    }

    /**
     * @param int $countSuit
     */
    private function separateTests(int $countSuit): void
    {
        $testInfoCollection = $this->separateTestsHandler->buildTestInfoCollection();
        $entityWithTime     = $this->separateTestsHandler->groupTimeEntityWithCountedTime($testInfoCollection);
        $arGroups           = $this->separateTestsHandler->separateDirectoriesByTime($entityWithTime, $countSuit);

        // remove all group files
        $this->separateTestsHandler->removeAllGroupFiles();

        /** @var GroupBlockInfo $arGroupBlockInfo */
        foreach ($arGroups as $groupNumber => $arGroupBlockInfo) {
            $groupName = $this->groupPrefix . $groupNumber;

            foreach ($arGroupBlockInfo->getDirTimes() as $localTestsDir => $time) {
                file_put_contents($this->separateTestsHandler->getGroupDirectoryPath() . $groupName . '.txt', $localTestsDir . PHP_EOL, FILE_APPEND);
            }
        }
    }
}
