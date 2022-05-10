<?php

namespace PsyPvoutput;

use DateTime;
use Exception;
use InvalidArgumentException;

/**
 * PsyFactory PvOutput status model
 */
class Status
{
    protected DateTime $dateTime;
    protected ?int $energyGeneration = null;
    protected $powerGeneration, $energyConsumption, $powerConsumption, $temperature, $voltage;


    protected $cumulativeFlag, $netFlag, $extendedValueV7, $extendedValueV8, $extendedValueV9, $extendedValueV10;
    protected $extendedValueV11, $extendedValueV12, $textMessage1;

    const CUMULATIVE_FLAG_LIFETIME = 1;
    const CUMULATIVE_FLAG_GENERATION_LIFETIME = 2;
    const CUMULATIVE_FLAG_CONSUMPTION_LIFETIME = 3;

    /**
     * Constructor
     * @param DateTime $dateTime
     */
    public function __construct(DateTime $dateTime)
    {
        $this->setDateTime($dateTime);
    }

    /**
     * Set the datetime of the status
     * @param DateTime $dateTime
     * @return void
     */
    public function setDateTime(DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    /**
     * Get the datetime of the status
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * Set the enery generation in watt hours
     * @param int $wattHours
     * @return void
     */
    public function setEnergyGeneration(int $wattHours): void
    {
        $this->energyGeneration = $wattHours;
    }

    /**
     * Get the energy generation in watt hours
     * @return int
     * @throws Exception
     */
    public function getEnergyGeneration(): int
    {
        if (!$this->hasEnergyGeneration()) {
            throw new Exception(__METHOD__ . '; No energy generation set');
        }

        return $this->energyGeneration;
    }

    /**
     * Check if the energy generation in watt hours is set
     * @return bool
     */
    public function hasEnergyGeneration(): bool
    {
        return !is_null($this->energyGeneration);
    }

    /**
     * Set the power generation in watts
     * @param int $watts
     * @return void
     */
    public function setPowerGeneration(int $watts): void
    {
        $this->powerGeneration = $watts;
    }

    /**
     * Get the power generation in watts
     * @return int
     * @throws Exception
     */
    public function getPowerGeneration(): int
    {
        if (!$this->hasPowerGeneration()) {
            throw new Exception(__METHOD__ . '; No power generation set');
        }

        return $this->powerGeneration;
    }

    /**
     * Check if the power generation in watts is set
     * @return bool
     */
    public function hasPowerGeneration(): bool
    {
        return !is_null($this->powerGeneration);
    }

    /**
     * Set the temperature in celsius
     * @param float $celsius
     * @return void
     */
    public function setTemperature(float $celsius): void
    {
        $this->temperature = $celsius;
    }

    /**
     * Get the temperature in celsius
     * @return float
     */
    public function getTemperature(): float
    {
        if (!$this->hasTemperature()) {
            throw new Exception(__METHOD__ . '; No temperature set');
        }

        return $this->temperature;
    }

    /**
     * Check if the temperature in celsius is set
     * @return bool
     */
    public function hasTemperature(): bool
    {
        return !is_null($this->temperature);
    }

    /**
     * Set the voltage in volts
     * @param float $volts
     * @return void
     */
    public function setVoltage(float $volts): void
    {
        $this->voltage = $volts;
    }

    /**
     * Get the voltage in volts
     * @return float
     * @throws Exception
     */
    public function getVoltage(): float
    {
        if (!$this->hasVoltage()) {
            throw new Exception(__METHOD__ . '; No temperature set');
        }

        return $this->voltage;
    }

    /**
     * Check if the voltage in volts is set
     * @return bool
     */
    public function hasVoltage(): bool
    {
        return !is_null($this->voltage);
    }

    /**
     * Set the cummulative flag
     * @param int|null $flag
     * @return void
     * @throws InvalidArgumentException
     */
    public function setCumulativeFlag(int $flag = null): void
    {
        if (!is_null($flag) && !in_array($flag, [self::CUMULATIVE_FLAG_LIFETIME, self::CUMULATIVE_FLAG_GENERATION_LIFETIME, self::CUMULATIVE_FLAG_CONSUMPTION_LIFETIME])) {
            throw new InvalidArgumentException(__METHOD__ . '; Invalid flag');
        }

        $this->cumulativeFlag = $flag;
    }

    /**
     * Get the cumulative flag
     * @return int
     * @throws Exception
     */
    public function getCumulativeFlag(): int
    {
        if (!$this->hasCumulativeFlag()) {
            throw new Exception(__METHOD__ . '; No cumulative flag set');
        }

        return $this->cumulativeFlag;
    }

    /**
     * Check if the cumulative flag is set
     * @return bool
     */
    public function hasCumulativeFlag(): bool
    {
        return !is_null($this->cumulativeFlag);
    }

    /**
     * Convert status object to an array to send to PvOutput
     * @return array
     */
    public function toArray(): array
    {
        $dateTime = $this->getDateTime();

        $data = [
            'd' => $dateTime->format('Ymd'),
            't' => $dateTime->format('H:i')
        ];

        if ($this->hasEnergyGeneration()) {
            $data['v1'] = $this->getEnergyGeneration();
        }

        if ($this->hasPowerGeneration()) {
            $data['v2'] = $this->getPowerGeneration();
        }

        if ($this->hasTemperature()) {
            $data['v5'] = $this->getTemperature();
        }

        if ($this->hasVoltage()) {
            $data['v6'] = $this->getVoltage();
        }

        if ($this->hasCumulativeFlag()) {
            $data['c1'] = $this->getCumulativeFlag();
        }

        return $data;
    }
}
