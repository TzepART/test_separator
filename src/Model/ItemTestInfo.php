<?php
declare(strict_types=1);


namespace TestSeparator\Model;


class ItemTestInfo
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
     * @var string
     */
    private $relativePath;

    /**
     * ItemTestInfo constructor.
     *
     * @param string $dir
     * @param string $file
     * @param string $relativePath
     * @param string $test
     * @param int $time
     */
    public function __construct(string $dir, string $file, string $relativePath, string $test, int $time)
    {
        $this->dir          = $dir;
        $this->file         = $file;
        $this->relativePath = $relativePath;
        $this->test         = $test;
        $this->time         = $time;
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

    /**
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function asArray()
    {
        return [
            $this->dir,
            $this->file,
            $this->relativePath,
            $this->test,
            $this->time,
        ];
    }
}
