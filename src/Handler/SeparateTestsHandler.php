<?php
declare(strict_types=1);


namespace TestSeparator\Handler;

use drupol\phpartition\Algorithm\Greedy;


class SeparateTestsHandler
{
    /**
     * @param string $suitesFile
     * @param string $resultFile
     * @param string $baseTestDirPath
     */
    public function reFormateSuitesFile(string $suitesFile, string $resultFile, string $baseTestDirPath): void
    {
        $strings        = file($suitesFile);
        $patternCommand = 'grep -R "%s" -l ' . $baseTestDirPath . '%s | head -1';
        file_put_contents($resultFile, 'dir; file; test; time;' . PHP_EOL);

// remove head line
        array_shift($strings);

        foreach ($strings as $index => $string) {
            $string   = str_replace('"', '', $string);
            $strArray = explode(',', $string);
            preg_match('/Support\.([a-z]+)\.([a-zA-Z]+)/', $strArray[1], $matches);
            $dir  = $matches[1];
            $test = $matches[2];
            $time = (int) $strArray[2];
            $file = trim(shell_exec(sprintf($patternCommand, $test, $dir)));
            file_put_contents($resultFile, sprintf('%s; %s; %s; %d;' . PHP_EOL, $dir, $file, $test, $time), FILE_APPEND);
        }
    }


    /**
     * @param string $resultFile
     * @param string $timeResultsFile
     * @param string $baseTestDirPath
     *
     * @return void
     */
    public function summTimeByDirectories(string $resultFile, string $timeResultsFile, string $baseTestDirPath): void
    {
        $strings = file($resultFile);
        array_shift($strings);
        $timeResults = [];
        $summ        = 0;
        foreach ($strings as $index => $string) {
            $arResult = explode(';', $string);
            $rootDir  = $arResult[0];
            preg_match('/([A-Za-z]+)\//', trim(str_replace($baseTestDirPath . $rootDir . '/', '', $arResult[1])), $matches);
            $dir    = $matches[1];
            $keyDir = $rootDir . '/' . $dir;
            $time   = round(((int) $arResult[3]) / 1000, 2);
            if (isset($timeResults[$keyDir])) {
                $timeResults[$keyDir] = round($timeResults[$keyDir] + $time, 2);
            } else {
                $timeResults[$keyDir] = $time;
            }
            $summ += $time;
        }
        file_put_contents($timeResultsFile, json_encode($timeResults));
    }


    /**
     * @param string $timeResultsFile
     * @param string $groupSeparatingFile
     * @param int    $countSuit
     *
     * @return void
     */
    public function separateDirectoriesByTime(string $timeResultsFile, string $groupSeparatingFile, int $countSuit): void
    {
        $timeResults = json_decode(file_get_contents($timeResultsFile), true);
        $greedy      = new Greedy();
        $greedy->setData($timeResults);
        $greedy->setSize($countSuit);
        $result = $greedy->getResult();

        $resultWithDir = [];
        foreach ($result as $key => $block) {
            foreach ($block as $time) {
                $keyDir                                   = array_search($time, $timeResults);
                $resultWithDir[$key]['dir_time'][$keyDir] = $time;
            }
            $resultWithDir[$key]['summ_time'] = array_sum($block);
        }
        file_put_contents($groupSeparatingFile, json_encode($resultWithDir));
    }

    public function scanAllDir($dir)
    {
        $result = [];
        foreach (scandir($dir) as $filename) {
            if ($filename[0] === '.') continue;
            $filePath = $dir . '/' . $filename;
            if (is_dir($filePath)) {
                foreach ($this->scanAllDir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } else {
                $result[] = $filename;
            }
        }

        return $result;
    }

    /**
     * @param string $groupDirPath
     */
    public function removeAllGroupFiles(string $groupDirPath): void
    {
        $files = scandir($groupDirPath); // get all file names
        foreach ($files as $file) { // iterate files
            $filePath = $groupDirPath . $file;
            if (is_file($filePath)) {
                unlink($filePath); // delete file
            }
        }
    }

}
