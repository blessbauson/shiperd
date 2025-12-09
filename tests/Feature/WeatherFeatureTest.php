<?php

/*
    NOTE: To run, in your terminal ->  php artisan test --testsuite=Feature
*/

use Illuminate\Support\Facades\Cache;
use function Pest\Laravel\get;
use App\Services\WeatherService;


beforeEach(function () {
    Cache::flush();
});

it('returns weather for a valid city (fresh)', function () {

    /*
        NOTES:
        1. Use Mockery, to sinulate behavior of WeatherService to return a valid geocode and weather.
        2. Mockery replaces real API call with "fake" version 
    */

    $weatherServiceMock = Mockery::mock(WeatherService::class);

    $weatherServiceMock->shouldReceive('get_geocode')
        ->once()
        ->andReturn([[
            'lat' => 14.6331,
            'lon' => 121.0994,
            'name' => 'Marikina'
        ]]);

    $weatherServiceMock->shouldReceive('get_weather')
        ->once()
        ->andReturn([
            'name' => 'Marikina',
            'main' => ['temp' => 299.3],
            'weather' => [['description' => 'light rain']],
            'dt' => 1234567890
        ]);

    $this->app->instance(WeatherService::class, $weatherServiceMock);

    $response = get('/api/weather/Marikina');

    /*
        NOTES:
        1. assertJsonStructure uses the wrapper data, similar to how the JsonResource is being returned in the controller
        2. assertJsonPath verfies the exact values inside the data object
    */

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['city', 'temp', 'weather', 'timestamp', 'source'],
        ])
        ->assertJsonPath('data.source', 'external')
        ->assertJsonPath('data.city', 'Marikina')
        ->assertJsonPath('data.temp', 299.3)
        ->assertJsonPath('data.weather', 'light rain')
        ->assertJsonPath('data.timestamp', 1234567890);
});