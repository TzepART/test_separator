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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln('Separating started...');

        $suitesFile   = $input->getArgument('allure_file');
        $baseTestDirPath = $input->getArgument('tests_directory');
        $groupDirPath = $input->getArgument('result_path');
        $countSuit    = (int) $input->getArgument('count_group');

        $this->separateTests($suitesFile, $baseTestDirPath, $groupDirPath, $countSuit);

        $output->writeln('Your test are separated!');

        return 0;
    }

    private function separateTests(string $suitesFile, string $baseTestDirPath, string $groupDirPath, int $countSuit)
    {
        $groupPrefix         = 'time_group_';
        $reFormateResultFile = __DIR__ . '/../../results/result.csv';
        $timeResultsFile     = __DIR__ . '/../../results/time_results.json';
        $groupSeparatingFile = __DIR__ . '/../../results/time_results_' . $countSuit . '_sutecases.json';

        $this->separateTestsHandler->reFormateSuitesFile($suitesFile, $reFormateResultFile, $baseTestDirPath);
        $this->separateTestsHandler->summTimeByDirectories($reFormateResultFile, $timeResultsFile, $baseTestDirPath);
        $this->separateTestsHandler->separateDirectoriesByTime($timeResultsFile, $groupSeparatingFile, $countSuit);

        $arGroups = json_decode(file_get_contents($groupSeparatingFile), false);

        // remove all group files
        $this->separateTestsHandler->removeAllGroupFiles($groupDirPath);

        foreach ($arGroups as $groupNumber => $arGroupBlockInfo) {
            $groupName        = $groupPrefix . $groupNumber;
            $arGroupBlockInfo = (array) $arGroupBlockInfo;

            foreach ($arGroupBlockInfo['dir_time'] as $localTestsDir => $time) {
                file_put_contents($groupDirPath . $groupName . '.txt', 'tests/' . $localTestsDir . PHP_EOL, FILE_APPEND);
            }
        }
    }
}
