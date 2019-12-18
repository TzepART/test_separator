<?php
declare(strict_types=1);


namespace TestSeparator\Model;


class TestInfo
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $test;

    /**
     * @var int
     */
    private $time;

    /**
     * TestInfo constructor.
     *
     * @param string $dir
     * @param string $file
     * @param string $test
     * @param int    $time
     */
    public function __construct(string $dir, string $file, string $test, int $time)
    {
        $this->dir  = $dir;
        $this->file = $file;
        $this->test = $test;
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getTest(): string
    {
        return $this->test;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    public function asArray()
    {
        return [
            $this->dir,
            $this->file,
            $this->test,
            $this->time,
        ];
    }
}
