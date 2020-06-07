<?php
declare(strict_types=1);

namespace TestSeparator\Model;

/**
 * Class GroupBlockInfo
 * @package TestSeparator\Model
 */
class GroupBlockInfo
{
    /**
     * @var array
     */
    private $dirTimes = [];

    /**
     * @var float
     */
    private $summTime = 0;


    /**
     * @return array
     */
    public function getDirTimes(): array
    {
        return $this->dirTimes;
    }

    public function addDirTime(string $keyDir, float $time): self
    {
        $this->dirTimes[$keyDir] = $time;

        return $this;
    }

    /**
     * @param float $time
     *
     * @return $this
     */
    public function increaseSumm(float $time): self
    {
        $this->summTime += $time;

        return $this;
    }

    /**
     * @param float $summTime
     *
     * @return $this
     */
    public function setSummTime(float $summTime): self
    {
        $this->summTime = $summTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getSummTime(): float
    {
        return $this->summTime;
    }

    public function asArray(): array
    {
        return [
            'dir_time'  => $this->dirTimes,
            'summ_time' => $this->summTime,
        ];
    }
}
