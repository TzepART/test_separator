<?php
declare(strict_types=1);

namespace TestSeparator\Service\Validator;

class SeparatedEntityValidator
{
    private const DIRECTORY_TYPE = 'directory';
    private const CLASS_TYPE = 'class';
    private const METHOD_TYPE = 'method';

    public function validateSeparatedEntity(string $separatedEntity): bool
    {
        switch ($this->getEntityType($separatedEntity)) {
            case self::DIRECTORY_TYPE:
            case self::CLASS_TYPE:
                return true;
            case self::METHOD_TYPE:
                return $this->validateMethodType($separatedEntity);
            default:
                return false;
        }
    }

    private function getEntityType(string $separatedEntity): ?string
    {
        if (is_dir($separatedEntity)) {
            return self::DIRECTORY_TYPE;
        }

        if (is_file($separatedEntity)) {
            return self::CLASS_TYPE;
        }

        if (count(explode(':', $separatedEntity)) === 2) {
            return self::METHOD_TYPE;
        }

        return null;
    }

    private function validateMethodType(string $separatedEntity): bool
    {
        $pathArray = explode(':', $separatedEntity);
        $filePath = trim($pathArray[0]);
        $method = trim($pathArray[1]);

        if (is_file($filePath) && strpos(file_get_contents($filePath), $method) !== false) {
            return true;
        }

        return false;
    }
}