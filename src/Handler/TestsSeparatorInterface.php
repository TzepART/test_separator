<?php
declare(strict_types=1);

namespace TestSeparator\Handler;

interface TestsSeparatorInterface
{
    public function separateTests(int $countSuit): void;
}