<?php
declare(strict_types=1);

namespace TestSeparator\Service;

class FileSystemHelper
{
    public static function checkFilesInDir(string $directoryPath): bool
    {
        if (is_dir($directoryPath)) {
            return count(
                    array_filter(
                        scandir($directoryPath),
                        static function (string $fileName) {
                            return !in_array($fileName, ['.', '..']);
                        }
                    )
                ) > 0;
        }

        return false;
    }

    public static function removeAllFilesInDirectory(string $directoryPath): void
    {
        $files = scandir($directoryPath); // get all file names
        foreach ($files as $file) { // iterate files
            $filePath = $directoryPath . $file;
            if (is_file($filePath)) {
                unlink($filePath); // delete file
            }
        }
    }

    public static function getFilePathsByDirectory(string $workDir): array
    {
        $files     = scandir($workDir);
        $filePaths = [];
        foreach ($files as $file) {
            $filePath = $workDir . $file;
            if (is_file($filePath)) {
                $filePaths[] = $filePath;
            }
        }

        return $filePaths;
    }
}
