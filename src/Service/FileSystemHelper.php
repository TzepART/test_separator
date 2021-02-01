<?php
declare(strict_types=1);

namespace TestSeparator\Service;

class FileSystemHelper
{
    const EXCLUDED_FILES = [
        '.',
        '..',
        '.gitkeep',
    ];

    public static function checkNotEmptyFilesInDir(string $directoryPath): bool
    {
        if (is_dir($directoryPath)) {
            return self::getCountNotEmptyFilesInDir($directoryPath) > 0;
        }

        return false;
    }

    public static function removeAllFilesInDirectory(string $directoryPath): void
    {
        $files = scandir($directoryPath); // get all file names
        foreach ($files as $file) { // iterate files
            $filePath = $directoryPath . $file;
            if (is_file($filePath) && !in_array($file, self::EXCLUDED_FILES)) {
                unlink($filePath); // delete file
            }
        }
    }

    public static function copyAllFilesFromDirToDir(string $directoryFrom, string $directoryTo): void
    {
        $files = scandir($directoryFrom); // get all file names
        foreach ($files as $file) { // iterate files
            $filePath = $directoryFrom . $file;
            $filePathTo = $directoryTo . $file;
            if (is_file($filePath) && !in_array($file, self::EXCLUDED_FILES)) {
                copy($filePath, $filePathTo);
            }
        }
    }

    public static function getFilePathsByDirectory(string $workDir): array
    {
        $files = scandir($workDir);
        $filePaths = [];
        foreach ($files as $file) {
            $filePath = $workDir . $file;
            if (is_file($filePath) && !in_array($file, self::EXCLUDED_FILES)) {
                $filePaths[$file] = $filePath;
            }
        }

        return $filePaths;
    }

    public static function getCountNotEmptyFilesInDir(string $directoryPath): int
    {
        return count(
            array_filter(
                scandir($directoryPath),
                static function (string $fileName) use ($directoryPath){
                    if(!in_array($fileName, self::EXCLUDED_FILES)){
                        clearstatcache();
                        $filePath = $directoryPath.$fileName;
                        if (is_file($filePath) && !empty(trim(file_get_contents($filePath)))) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }
}
