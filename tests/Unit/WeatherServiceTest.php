<?php
/*
    NOTE: To run, in your terminal -> vendor/bin/pest tests/Unit
*/

use App\Http\Controllers\API\WeatherController;
use App\Services\WeatherService;
use Mockery;


beforeEach(function () {
    /*
        NOTES:
        1. Use Mockery, to sinulate behavior of WeatherService to return a valid geocode and weather.
        2. Mockery replaces real API call with "fake" version 
    */

    $this->mockService = Mockery::mock(WeatherService::class);

    //Instantiate controller with mocked service
    $this->controller = new WeatherController($this->mockService);
});

afterEach(function () {
    Mockery::close();
});


it('buildResponse returns correct array structure', function () 
{
    $apiResult = [
        'name'      => 'Marikina',
        'main'      => ['temp' => 299.3],
        'weather'   => [['description' => 'light rain']],
        'dt'        => 1234567890
    ];

    //Private/protected methods (processWeather and buildResponse) are accessed via Reflection.
    $method = new ReflectionMethod(WeatherController::class, 'buildResponse');

    $response = $method->invoke($this->controller, $apiResult, 'external');

    expect($response)->toMatchArray([
        'source'    => 'external',
        'city'      => 'Marikina',
        'temp'      => 299.3,
        'weather'   => 'light rain',
        'timestamp' => 1234567890
    ]);
});
