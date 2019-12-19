<?php
declare(strict_types=1);


namespace TestSeparator\Handler;


class ServicesSeparateTestsFactory
{
    public static function makeLevelDeepService(string $serviceClassName): LevelDeepInterface
    {
        if(class_exists($serviceClassName)){
            return new $serviceClassName;
        }else{
            return new DirectoryDeepService();
        }
    }

    public static function makeFileSystemHelper(string $serviceClassName): FileSystemHelper
    {
        return new FileSystemHelper();
    }
}
