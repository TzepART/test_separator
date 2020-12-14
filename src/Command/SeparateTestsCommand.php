<?php
declare(strict_types=1);

namespace TestSeparator\Command;

use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SeparateTestsCommand constructor.
     *
     * @param SeparateTestsHandler $separateTestsHandler
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(SeparateTestsHandler $separateTestsHandler, LoggerInterface $logger, string $name = null)
    {
        parent::__construct($name);
        $this->separateTestsHandler = $separateTestsHandler;
        $this->logger = $logger;
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

        $output->writeln('Your tests are separated!');

        return 0;
    }

    /**
     * @param int $countSuit
     */
    private function separateTests(int $countSuit): void
    {
        $testInfoCollection = $this->separateTestsHandler->buildTestInfoCollection();
        $entityWithTime = $this->separateTestsHandler->groupTimeEntityWithCountedTime($testInfoCollection);
        $arGroups = $this->separateTestsHandler->separateDirectoriesByTime($entityWithTime, $countSuit);

        // remove all group files
        $this->separateTestsHandler->removeAllGroupFiles();

        /** @var GroupBlockInfo $arGroupBlockInfo */
        foreach ($arGroups as $groupNumber => $arGroupBlockInfo) {
            $groupName = $this->groupPrefix . $groupNumber;
            $filePath = $this->separateTestsHandler->getGroupDirectoryPath() . $groupName . '.txt';

            foreach ($arGroupBlockInfo->getDirTimes() as $localTestsDir => $time) {
                file_put_contents($filePath, $localTestsDir . PHP_EOL, FILE_APPEND);
            }

            if (file_exists($filePath)) {
                if (!filesize($filePath)) {
                    $this->logger->info(sprintf('File for %s is empty.', $groupName));
                } else {
                    $this->logger->info(sprintf('File for %s was created successfully.', $groupName));
                }
            } else {
                $this->logger->notice(sprintf('File for %s doesn\'t exist.', $groupName));
            }
        }
    }
}
