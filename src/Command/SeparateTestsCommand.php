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
     * @param string|null          $name
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
            ->addArgument('allure_file', InputArgument::REQUIRED, 'The file with preview tests results.')
            ->addArgument('tests_directory', InputArgument::REQUIRED, 'The directory with tests.')
            ->addArgument('result_path', InputArgument::REQUIRED, 'The directory where results will.')
            ->addArgument('count_group', InputArgument::REQUIRED, 'The count of time groups.')
            ->setHelp('This command allows you to separate tests...');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln('Separating started...');

        $suitesFile      = $input->getArgument('allure_file');
        $baseTestDirPath = $input->getArgument('tests_directory');
        $groupDirPath    = $input->getArgument('result_path');
        $countSuit       = (int) $input->getArgument('count_group');

        $this->separateTests($suitesFile, $baseTestDirPath, $groupDirPath, $countSuit);

        $output->writeln('Your test are separated!');

        return 0;
    }

    /**
     * @param string $suitesFile
     * @param string $baseTestDirPath
     * @param string $groupDirPath
     * @param int    $countSuit
     */
    private function separateTests(string $suitesFile, string $baseTestDirPath, string $groupDirPath, int $countSuit)
    {
        $this->separateTestsHandler->setBaseTestDirPath($baseTestDirPath);

        $testInfoArray    = $this->separateTestsHandler->reFormateSuitesFile($suitesFile);
        $testDirsWithTime = $this->separateTestsHandler->summTimeByDirectories($testInfoArray);
        $arGroups         = $this->separateTestsHandler->separateDirectoriesByTime($testDirsWithTime, $countSuit);

        // remove all group files
        $this->separateTestsHandler->removeAllGroupFiles($groupDirPath);

        /** @var GroupBlockInfo $arGroupBlockInfo */
        foreach ($arGroups as $groupNumber => $arGroupBlockInfo) {
            $groupName = $this->groupPrefix . $groupNumber;

            foreach ($arGroupBlockInfo->getDirTimes() as $localTestsDir => $time) {
                file_put_contents($groupDirPath . $groupName . '.txt', 'tests/' . $localTestsDir . PHP_EOL, FILE_APPEND);
            }
        }
    }
}
