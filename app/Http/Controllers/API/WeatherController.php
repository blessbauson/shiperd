<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

use App\Http\Requests\API\GetWeatherRequest;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    protected WeatherService $weatherService;

    
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }


    /**
     * Always fetch weather data directly from the API
    */
    public function getWeather(GetWeatherRequest $request)
    {
        $data = $this->processWeather($request, 'external');
        return $this->jsonResponse($data);
    }

   
    /**
     * Fetch weather data using cache when available, otherwise fetch from API
     */
    public function getWeatherCached(GetWeatherRequest $request)
    {
        $city     = $request->city;
        $cacheKey = "weather_{$city}";

        if (Cache::has($cacheKey)) {
            $data           = Cache::get($cacheKey);
            $data['source'] = 'cache';
        } else {
            // Not cached, call api
            $data = $this->processWeather($request, 'external');

            // Store in cache for 10 minutes
            Cache::put($cacheKey, $data, now()->addMinutes(10));
        }

        return $this->jsonResponse($data);
    }


    /**
     * Fetch and process weather from WeatherService API
     */
    protected function processWeather(GetWeatherRequest $request, string $source): array|false
    {
        $validated  = $request->validated();
        $apiGeocode = $this->weatherService->get_geocode($validated);

        if (empty($apiGeocode[0]['lat']) || empty($apiGeocode[0]['lon'])) {
            return false;
        }

        $payload = [
            'lat' => $apiGeocode[0]['lat'],
            'lon' => $apiGeocode[0]['lon'],
        ];
        $apiWeather  = $this->weatherService->get_weather($payload);

        return $this->buildResponse($apiWeather, $source);
    }


    /**
     * Format API response into consistent structure based on requirement
     */
    protected function buildResponse(array $result, string $source): array
    {
        return [
            'source'    => ($source == "cache") ? "cache" : "external",
            'city'      => $result['name'] ?? "",
            'temp'      => $result['main']['temp'] ?? "",
            'weather'   => $result['weather'][0]['description'] ?? "",
            'timestamp' => $result['dt'] ?? "",
        ];
    }


    /**
     * Return standardized JSON response
     */
    private function jsonResponse(array|false $data)
    {
        return new JsonResource($data ?: []);
    }
}
