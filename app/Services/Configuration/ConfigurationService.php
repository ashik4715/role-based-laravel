<?php

namespace App\Services\Configuration;

use App\Interfaces\ConfigurationRepositoryInterface;
use App\Models\Configuration;
use Illuminate\Database\Eloquent\Collection;

class ConfigurationService
{
    private ConfigurationChangeLogsService $configurationChangeLogsServices;

    private ConfigurationRepositoryInterface $configRepository;

    public function __construct(ConfigurationRepositoryInterface $config_repository, ConfigurationChangeLogsService $configurationChangeLogsServices)
    {
        $this->configRepository = $config_repository;
        $this->configurationChangeLogsServices = $configurationChangeLogsServices;
    }

    public function getAll(): Collection
    {
        return $this->configRepository->getAllConfigurations();
    }

    public function getByKey(ConfigurationKeys $key): Configuration
    {
        return $this->configRepository->getConfigurationByKey($key);
    }

    public function updateByKey(ConfigurationKeys $key, $value, $logs): bool
    {
        if ($this->configRepository->updateConfigurationByKey($key, $value)) {
            return (bool) $this->configurationChangeLogsServices->createLogs($logs);
        }

        return false;
    }

    public function doesExceedResubmissionLimit(int $currentApplicationVersion): bool
    {
        return $this->configRepository->getConfigurationByKey(ConfigurationKeys::RESUBMIT_LIMIT)->value <= $currentApplicationVersion;
    }
}
