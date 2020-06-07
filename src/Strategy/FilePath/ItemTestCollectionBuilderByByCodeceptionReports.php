<?php
declare(strict_types=1);

namespace TestSeparator\Strategy\FilePath;

use TestSeparator\Model\ItemTestInfo;

class ItemTestCollectionBuilderByByCodeceptionReports extends AbstractItemTestCollectionBuilder
{
    /**
     * @var string
     */
    private $codeceptionReportDir;

    /**
     * ItemTestCollectionBuilderByByCodeceptionReports constructor.
     *
     * @param string $baseTestDirPath
     * @param string $codeceptionReportDir
     */
    public function __construct(string $baseTestDirPath, string $codeceptionReportDir)
    {
        parent::__construct($baseTestDirPath);
        $this->codeceptionReportDir = $codeceptionReportDir;
    }

    /**
     * @return ItemTestInfo[]|array
     */
    public function buildTestInfoCollection(): array
    {
        $filePaths = $this->getFilePathsByDirectory($this->codeceptionReportDir);

        $results = [];
        foreach ($filePaths as $filePath) {
            echo $filePath . PHP_EOL;
            //TODO add catching if xml is Invalid
            $xml = simplexml_load_string(file_get_contents($filePath));

            foreach ($xml->{'testsuite'}->children() as $testChild) {
                $file         = (string) $testChild->attributes()->file;
                // TODO creating relativePath to ItemTestInfo constructor
                $relativePath = str_replace($this->getBaseTestDirPath(), 'tests/', $file);
                $dir          = preg_replace('/[A-z0-9]+\.php$/', '', $relativePath);
                preg_match('/([^ ]+)/', (string) $testChild->attributes()->name, $matches);
                $test = $matches[1];
                $time = (int) (((float) $testChild->attributes()->time) * 1000);

                $results[] = new ItemTestInfo(
                    $dir,
                    $file,
                    $relativePath,
                    $test,
                    $time
                );
            }
        }

        return $results;
    }
}
