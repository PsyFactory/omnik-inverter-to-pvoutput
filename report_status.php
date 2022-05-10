#!/usr/bin/php
<?php

use PsyOmnik\Client as OmnikClient;
use PsyPvoutput\Client as PvOutputClient;
use PsyPvoutput\Status as PvOutputStatus;

require_once('vendor/autoload.php');

try {
    // Load .env configuration
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['INVERTER_IP', 'INVERTER_USERNAME', 'INVERTER_PASSWORD', 'PVOUTPUT_API_KEY', 'PVOUTPUT_SYSTEM_ID']);

    // Retrieve status from inverter
    $omnikClient = new OmnikClient($_ENV['INVERTER_IP'], $_ENV['INVERTER_USERNAME'], $_ENV['INVERTER_PASSWORD']);
    $omnikStatus = $omnikClient->retrieveStatus();

    // Create PVoutput status
    $pvOutputStatus = new PvOutputStatus(new DateTime());
    $pvOutputStatus->setEnergyGeneration((int)($omnikStatus->getTotalKwh() * 1000));
    $pvOutputStatus->setCumulativeFlag(PvOutputStatus::CUMULATIVE_FLAG_LIFETIME);

    // Send status to PVoutput
    $pvOutputClient = new PvOutputClient($_ENV['PVOUTPUT_SYSTEM_ID'], $_ENV['PVOUTPUT_API_KEY']);
    $pvOutputClient->addStatus($pvOutputStatus);

    echo date("Y-m-d H:i:s") . ": Successfully send energy generation to PVoutput\n";
} catch (Exception $e) {
    echo date("Y-m-d H:i:s") . ': Error sending energy generation to PVoutput; ' . $e->getMessage() . "\n";
}
