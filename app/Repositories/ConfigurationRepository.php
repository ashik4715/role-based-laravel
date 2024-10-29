<?php

namespace App\Repositories;

use App\Interfaces\ConfigurationRepositoryInterface;
use App\Models\Configuration;
use App\Services\Configuration\ConfigurationKeys;
use Illuminate\Database\Eloquent\Collection;

class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    public function getAllConfigurations(): Collection
    {
        return Configuration::all();
    }

    public function getConfigurationByKey(ConfigurationKeys $key): Configuration
    {
        return Configuration::where('key', $key)->firstOrFail();
    }

    public function updateConfigurationByKey(ConfigurationKeys $key, $value): bool
    {
        return (bool) Configuration::where('key', $key)->update(['value' => $value]);
    }
}
