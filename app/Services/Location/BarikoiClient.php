<?php

namespace App\Services\Location;

use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Exception\RequestException;

class BarikoiClient implements Client
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('barikoi.api_key');
        $this->baseUrl = "https://barikoi.xyz/v1/api/search";
    }

    public function getAddressFromGeo(Geo $geo): Address
    {
        $address = new BarikoiAddress();
        if ($geo->isEmpty()) return $address->setAddress(null);

        try {
            $client = new HTTPClient();
            $url = "$this->baseUrl/reverse/geocode/server/$this->apiKey/place";
            $params = [
                'latitude' => $geo->getLat(),
                'longitude' => $geo->getLng(),
                'address' => 'true',
                'area' => 'true'
            ];
            $response = $client->request('GET', $url, [
                'query' => $params
            ]);
            $response = json_decode($response->getBody());

            # $this->log($url, $params, $response);

            if (!isset($response->place)) return $address->setAddress(null);
            $place = $response->place;
            return $address->handleFullAddress($place);
        } catch (RequestException $e) {
            throw $e;
        }
    }
}
