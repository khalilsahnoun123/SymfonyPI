<?php

namespace App\Controller\velo;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherController
{
    private HttpClientInterface $client;
    private const GEO_URL = 'https://geocoding-api.open-meteo.com/v1/search';
    private const WEATHER_URL = 'https://api.open-meteo.com/v1/forecast';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getWeatherForLocation(string $location): ?array
    {
        try {
            $geoResponse = $this->client->request('GET', self::GEO_URL, [
                'query' => [
                    'name' => $location,
                    'count' => 1,
                    'language' => 'en',
                    'format' => 'json',
                ]
            ]);

            $geoData = $geoResponse->toArray();

            if (empty($geoData['results'])) {
                return null;
            }

            $lat = $geoData['results'][0]['latitude'];
            $lon = $geoData['results'][0]['longitude'];

            $weatherResponse = $this->client->request('GET', self::WEATHER_URL, [
                'query' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current' => 'temperature_2m,precipitation,rain,is_day',
                ]
            ]);

            $weatherData = $weatherResponse->toArray();
            $current = $weatherData['current'] ?? [];

            return [
                'temperature' => $current['temperature_2m'] ?? null,
                'precipitation' => $current['precipitation'] ?? null,
                'rain' => $current['rain'] ?? null,
                'is_day' => $current['is_day'] ?? null,
                'good_for_bike' => ($current['temperature_2m'] ?? 0) > 18
                                    && ($current['precipitation'] ?? 1) == 0
                                    && ($current['rain'] ?? 1) == 0,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
