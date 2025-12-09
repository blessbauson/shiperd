<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WeatherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
/*
    NOTES:
    1. No authentication or client middleware is required since the endpoints are publicly accessible.
    2. Rate limiting is still applied per client. By default, the limit is 60 requests per minute.
*/

$rate_limit = config('api.api_limit');

Route::middleware("throttle:$rate_limit,1")->group(function () {
    Route::get('/weather/{city}', [WeatherController::class, 'getWeather']);
    Route::get('/weather/{city}/cached', [WeatherController::class, 'getWeatherCached']);
});