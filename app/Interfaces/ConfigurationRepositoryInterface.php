<?php

namespace App\Interfaces;

use App\Models\Configuration;
use App\Services\Configuration\ConfigurationKeys;
use Illuminate\Database\Eloquent\Collection;

interface ConfigurationRepositoryInterface
{
    public function getAllConfigurations(): Collection;

    public function getConfigurationByKey(ConfigurationKeys $key): Configuration;

    public function updateConfigurationByKey(ConfigurationKeys $key, $value): bool;
}
