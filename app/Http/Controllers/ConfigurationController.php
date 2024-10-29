<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConfigurationRequest;
use App\Services\Configuration\ConfigurationKeys;
use App\Services\Configuration\ConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConfigurationController extends Controller
{
    private ConfigurationService $configurations;

    /**
     * @param  ConfigurationService  $configuration_services
     */
    public function __construct(ConfigurationService $configuration_services)
    {
        $this->configurations = $configuration_services;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        return response($this->configurations->getAll(), 200);
    }

    /**
     * @param  Request  $request
     * @param $config
     * @return Response
     */
    public function show(Request $request, $config): Response
    {
        $key_name = ConfigurationKeys::from($config);
        $result_value = $this->configurations->getByKey($key_name);

        return $result_value ? response($result_value, 200) : response('The desired key is not found.', Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConfigurationRequest $request): Response
    {
        $key_name = ConfigurationKeys::from($request->input('key'));
        $value_name = $request->input('value');
        $old_value = $this->configurations->getByKey($key_name);

        $this->configurations->updateByKey($key_name, $value_name, [
            'configuration_id' => $old_value->id,
            'from' => $old_value->value,
            'to' => $value_name,
        ]);

        return response('Updated', 200);
    }
}
