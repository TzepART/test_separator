<?php
declare(strict_types=1);


namespace TestSeparator\Strategy\FilePath;


class ItemTestCollectionBuilderByClassMapSystem extends AbstractItemTestCollectionBuilder
{
    private $testClassesMap = [];

    public function buildTestInfoCollection(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getTestClassesMap(): array
    {
        return $this->testClassesMap;
    }


    /**
     * TODO add logic of creating TestClassesMap
     * Tree with namespaces of test classes and test (as methods)
     * @return $this
     */
    public function setTestClassesMap()
    {
        $this->testClassesMap = [];

        return $this;
    }
}
