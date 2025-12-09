## Weather API (Laravel 12)
A simple Laravel 12 API for fetching weather information by city. This API integrates with a third-party weather service, implements caching for 10 minutes, and supports rate limiting per client.

## Features
- Fetch Weather Data: Retrieve current weather for any city.
- Caching: Responses are cached for 10 minutes to reduce API calls.
- Rate Limiting: Limits requests to 60 per minute per client.
- Public API: No authentication required, endpoints are publicly accessible.
- Feature & Unit Tests: Includes Pest tests for API endpoints and controller logic.
- Proper validation is applied via GetWeatherRequest.

## Setup Process
1. Create new project named "shiperd" using docker
2. Setup public repository in github, clone repo
3. Install and Setup Laravel 12 without any toolkit included
4. Install and configure PEST
5. Create DB
6. Configure env file
7. Run initial migration, note that cache is in DB while session is in file

## API Endpoints
1. Fetch weather
**API will be available at http://localhost:8000/api/weather/{city}**
2. Fetch weather in cache
**API will be available at http://localhost:8000/api/weather/{city}/cached**

## Testing

This project uses Pest for testing.
 - Run Feature Tests
 **php artisan test --testsuite=Feature**
- Run Unit Tests
 **php artisan test --testsuite=Unit**

## ENV File
API_LIMIT=60
MAXATTEMPTS=5
API_TIMEOUT_IN_SECS=10
API_KEY="your_api_key_value"