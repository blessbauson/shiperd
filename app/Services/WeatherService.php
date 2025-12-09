<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class WeatherService
{
    protected $timeout_in_secs;
    protected $api_base_url;
    protected $api_key;

    const CONTENT_TYPE = 'application/json';


    public function __construct()
    {
        $this->timeout_in_secs  = config('api.api_timeout_in_secs');
        $this->api_base_url     = config('api.api_baseurl');
        $this->api_key          = config('api.api_key');
    }


    /**
     * Call to geocode api endpoint, to fetch lat and long at passed city name in payload
     */
    public function get_geocode(array $data)
    {
        $data['q']      = $data['city'];
        $data['appid']  = $this->api_key;

        $api_url     = $this->api_base_url."/geo/1.0/direct";
        $api_url_get = $api_url.'?'.Arr::query($data);

        $response   = Http::withHeaders([
                        'Content-Type'  => self::CONTENT_TYPE,
                        'Accept'        => self::CONTENT_TYPE
                    ])
                    ->timeout($this->timeout_in_secs)
                    ->get($api_url_get);

        if ($response->failed()) {
            throw new \Exception("API failed.");
        }

        return $response->json();
    }


     /**
     * Call to current weather api endpoint, using lat and long payload
     */
    public function get_weather(array $data)
    {
        $data['appid'] = $this->api_key;

        $api_url     = $this->api_base_url."/data/2.5/weather";
        $api_url_get = $api_url.'?'.Arr::query($data);

        $response   = Http::withHeaders([
                        'Content-Type'  => self::CONTENT_TYPE,
                        'Accept'        => self::CONTENT_TYPE
                    ])
                    ->timeout($this->timeout_in_secs)
                    ->get($api_url_get);

        if ($response->failed()) {
            throw new \Exception("API failed.");
        }

        return $response->json();
    }
}
