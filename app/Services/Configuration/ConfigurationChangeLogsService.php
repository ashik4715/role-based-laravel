<?php

namespace App\Services\Configuration;

use  App\Repositories\ConfigurationChangeLogsRepository;

class ConfigurationChangeLogsService
{
    private ConfigurationChangeLogsRepository $configurationChangeLogsRepository;

    public function __construct(ConfigurationChangeLogsRepository $configurationChangeLogsRepository)
    {
        $this->configurationChangeLogsRepository = $configurationChangeLogsRepository;
    }

    public function createLogs(array $logs)
    {
        return $this->configurationChangeLogsRepository->create($logs);
    }
}
