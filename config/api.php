<?php
    return [
        'api_timeout_in_secs'   => env('APLUS_API_TIMEOUT_IN_SECS', 10),
        'api_limit'             => env('API_LIMIT', 60), //Throttle setting
        'max_attempts'          => env('MAXATTEMPTS', 5),
        'api_key'               => env('API_KEY', 'API_KEY'),
        'api_baseurl'           => "https://api.openweathermap.org"
    ];
