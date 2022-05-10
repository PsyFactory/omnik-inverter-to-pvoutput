<?php

namespace PsyOmnik;

/**
 * PsyFactory Omnik internet status model
 */
class Status
{
    protected int $currentWatt;
    protected float $todayKwh;
    protected float $totalKwh;

    /**
     * Constructor
     * @param float $totalKwh
     * @param float $todayKwh
     * @param int $currentWatt
     */
    public function __construct(float $totalKwh, float $todayKwh, int $currentWatt)
    {
        $this->setTotalKwh($totalKwh);
        $this->setTodayKwh($todayKwh);
        $this->setCurrentWatt($currentWatt);
    }

    /**
     * Set the total KWH
     * @param float $totalKwh
     * @return void
     */
    public function setTotalKwh(float $totalKwh): void
    {
        $this->totalKwh = $totalKwh;
    }

    /**
     * Get the total Kwh
     * @return float
     */
    public function getTotalKwh()
    {
        return $this->totalKwh;
    }

    /**
     * Set the today Kwh
     * @param float $todayKwh
     * @return void
     */
    public function setTodayKwh(float $todayKwh): void
    {
        $this->todayKwh = $todayKwh;
    }

    /**
     * Get the today Kwh
     * @return float
     */
    public function getTodayKwh(): float
    {
        return $this->todayKwh;
    }

    /**
     * Set the current watt
     * @param int $currentWatt
     * @return void
     */
    public function setCurrentWatt(int $currentWatt): void
    {
        $this->currentWatt = $currentWatt;
    }

    /**
     * Get the current watt
     * @return int
     */
    public function getCurrentWatt(): int
    {
        return $this->currentWatt;
    }
}
