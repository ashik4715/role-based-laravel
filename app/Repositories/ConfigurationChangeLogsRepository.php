<?php

namespace App\Repositories;

use App\Models\ConfigurationChangeLogs;

class ConfigurationChangeLogsRepository
{
    public function create(array $logs)
    {
        return ConfigurationChangeLogs::create($logs);
    }
}
