<?php
declare(strict_types=1);

namespace Tests\Service;

use TestSeparator\Service\FileSystemHelper;
use PHPUnit\Framework\TestCase;

class FileSystemHelperTest extends TestCase
{
    private $tmpDefaultGroupPath = 'tests/data/tmp-default-groups/';
    private $defaultGroupPath = 'tests/data/default-groups/';
    private $emptyReportsPath = 'tests/data/empty-reports/';

    protected function tearDown()
    {
        parent::tearDown();
        $this->clearTmpDir();
    }

    /**
     * @dataProvider dataCheckNotEmptyFilesInDir()
     *
     * @param string $path
     * @param bool $expectedResult
     */
    public function testCheckNotEmptyFilesInDir(string $path, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, FileSystemHelper::checkNotEmptyFilesInDir($path));
    }

    public function dataCheckNotEmptyFilesInDir(): iterable
    {
        yield [
            $this->defaultGroupPath,
            true
        ];

        yield [
            $this->emptyReportsPath,
            false
        ];
    }

    public function testCopyAllFilesFromDirToDir(): void
    {
        mkdir($this->tmpDefaultGroupPath);
        FileSystemHelper::copyAllFilesFromDirToDir($this->defaultGroupPath, $this->tmpDefaultGroupPath);
        $this->assertEquals(
            [
                'time_group_0.txt' => 'tests/data/tmp-default-groups/time_group_0.txt',
                'time_group_1.txt' => 'tests/data/tmp-default-groups/time_group_1.txt',
            ],
            FileSystemHelper::getFilePathsByDirectory($this->tmpDefaultGroupPath)
        );

        FileSystemHelper::removeAllFilesInDirectory($this->tmpDefaultGroupPath);

        $this->assertEquals(
            [],
            FileSystemHelper::getFilePathsByDirectory($this->tmpDefaultGroupPath)
        );

        $this->clearTmpDir();
    }

    /**
     * @dataProvider dataGetCountNotEmptyFilesInDir()
     *
     * @param int $expectedFilesCount
     * @param string $dirPath
     */
    public function testGetCountNotEmptyFilesInDir(int $expectedFilesCount, string $dirPath): void
    {
        $this->assertEquals($expectedFilesCount, FileSystemHelper::getCountNotEmptyFilesInDir($dirPath));
    }

    public function dataGetCountNotEmptyFilesInDir(): iterable
    {
        yield [
            2,
            $this->defaultGroupPath
        ];

        yield [
            0,
            $this->emptyReportsPath
        ];
    }

    protected function clearTmpDir(): void
    {
        if(is_dir($this->tmpDefaultGroupPath)){
            FileSystemHelper::removeAllFilesInDirectory($this->tmpDefaultGroupPath);
            rmdir($this->tmpDefaultGroupPath);
        }
    }
}
