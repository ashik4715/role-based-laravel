<?php

namespace App\Services\Notification;

/**
 * @internal
 */
class Config
{
    private string $baseUrl;
    private string $xApiKey;

    public function __construct()
    {
        $this->baseUrl = config('notification.base_url');;
        $this->xApiKey = config('notification.x_api_key');;
    }

    /**
     * @param  string  $uri
     * @return string
     */
    public function makeUrl(string $uri): string
    {
        return trim($this->baseUrl, '/').'/'.trim($uri, '/');
    }

    public function getXApiKey()
    {
        return $this->xApiKey;
    }
}
