<?php

namespace Polygon\Logging\Http\Middleware;

use App\Services\Configuration\ConfigurationKeys;
use App\Services\Configuration\ConfigurationService;
use Carbon\Carbon;
use Closure;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Polygon\Logging\Types;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    private ConfigurationService $configurationService;

    /** @var Repository|Application|mixed */
    private mixed $traceLevel;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
        $this->traceLevel = Cache::remember('settings', 5 * 60, function () {
            $key = ConfigurationKeys::tryFrom('application_service_trace_level');
            $traceLevel = null;
            if ($key) {
                $traceLevel = $this->configurationService->getByKey($key);
            }
            $traceLevel = $traceLevel ? $traceLevel->value : config('logging.channels.logstash.trace_level');

            return ['trace_level' => $traceLevel];
        });
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->traceLevel['trace_level'] == Types::TRACE) {
            Log::channel('stderr')->debug('TRACE LOGGING:', debug_backtrace());
        }

        /** @var JsonResponse|Response $response */
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response): void
    {
        $time = Carbon::now()->format('Y-m-d H:i:s');
        $clientIp = implode(' - ', $request->getClientIps());
        $serverStatus = $response->getStatusCode();
        if (is_string($response->getContent())) {
            $content = json_decode($response->getContent());
        }

        $responseStatus = $response instanceof JsonResponse ? $response->getStatusCode() : (isset($content) ? $content->code.' -' : '100');
        $url = $request->getUri();
        $headers = json_encode($request->headers->all());

        if ($this->traceLevel == Types::INFO) {
            // Log::channel('logstash')->debug("$time $client_ip $url $server_status $response_status $headers");
            Log::channel('stderr')->debug("$time $clientIp $url $serverStatus $responseStatus $headers");
        }
    }
}
