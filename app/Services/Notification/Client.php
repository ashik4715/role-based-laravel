<?php

namespace App\Services\Notification;

use App\Services\Notification\Exceptions\NotificationServiceForbidden;
use App\Services\Notification\Exceptions\NotificationServiceNotWorking;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private HttpClient $httpClient;
    private Config $config;
    /** @var bool */
    private mixed $isCommunicateViaXApiKey;

    /**
     * @param  HttpClient  $httpClient
     * @param  Config  $config
     */
    public function __construct(HttpClient $httpClient, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
        $this->isCommunicateViaXApiKey = false;
    }

    /**
     * @throws GuzzleException
     * @throws NotificationServiceNotWorking
     * @throws NotificationServiceForbidden
     */
    public function get($uri): array
    {
        return $this->call("get", $uri);
    }

    /**
     * @param  ResponseInterface  $response
     * @param $assoc
     * @return mixed
     */
    private function decodeResponse(ResponseInterface $response, $assoc = true): mixed
    {
        $string = $response->getBody()->getContents();
        $result = json_decode($string, $assoc);
        if (json_last_error() != JSON_ERROR_NONE && $string != "") {
            $result = $string;
        }

        return $result;
    }

    /**
     * @param $method
     * @param $uri
     * @param  null  $data
     * @return array
     * @throws NotificationServiceNotWorking
     * @throws GuzzleException
     * @throws NotificationServiceForbidden
     */
    private function call($method, $uri, $data = null): array
    {
        try {
            $res = $this->httpClient->request(strtoupper($method), $this->config->makeUrl($uri), $this->getOptions($data));
            return $this->decodeResponse($res);
        } catch (RequestException $e) {
            if (!$e->hasResponse()) {
                throw new NotificationServiceNotWorking($e->getMessage());
            }

            $error = $e->getResponse();
            $res = $this->decodeResponse($error);
            $status = $error->getStatusCode();

            if ($error->getStatusCode() == 403) {
                throw new NotificationServiceForbidden($res ? $res['message'] : $error->getReasonPhrase(), $status);
            }

            throw new NotificationServiceNotWorking($res ? $res['message'] : $error->getReasonPhrase(), $status);
        }
    }

    /**
     * @param  null  $data
     * @return array
     */
    private function getOptions($data = null): array
    {
        $options['headers'] = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json'
        ];

        if ($this->isCommunicateViaXApiKey) {
            $options['headers']['x-api-key'] = $this->config->getXApiKey();
        } else {
            $options['headers']['authorization'] = 'Bearer '.request()->bearerToken();
        }

        if ($data) {
            $options['json'] = $data;
        }

        return $options;
    }

    /**
     * @param $uri
     * @param $data
     * @return array
     * @throws GuzzleException
     * @throws NotificationServiceNotWorking
     * @throws NotificationServiceForbidden
     */
    public function post($uri, $data): array
    {
        return $this->call("post", $uri, $data);
    }

    /**
     * @param $uri
     * @param $data
     * @return array
     * @throws GuzzleException
     * @throws NotificationServiceNotWorking|NotificationServiceForbidden
     */
    public function put($uri, $data): array
    {
        return $this->call("put", $uri, $data);
    }

    /**
     * @param  bool  $is_communicate_via_x_api_key
     * @return $this
     */
    public function isCommunicateViaXApiKey(bool $is_communicate_via_x_api_key = false): static
    {
        $this->isCommunicateViaXApiKey = $is_communicate_via_x_api_key;
        return $this;
    }
}
