<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherControllerTest extends TestCase
{
    public function test_fetch_weather_data()
    {
        Http::fake([
            'api.weatherapi.com/*' => Http::response([
                'location' => ['name' => 'Perth'],
                'current' => [
                    'temp_c' => 20.5,
                    'condition' => ['text' => 'Sunny']
                ]
            ], 200),
        ]);

        Cache::forget('current_weather_perth');

        $response = $this->getJson('/api/weather');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Perth',
            'temp_c' => 20.5,
            'text' => 'Sunny'
        ]);
    }

    public function test_cache_weather_data()
    {
        Cache::put('current_weather_perth', [
            'location' => ['name' => 'Perth'],
            'current' => [
                'temp_c' => 20.5,
                'condition' => ['text' => 'Sunny']
            ]
        ], 15);

        $response = $this->getJson('/api/weather');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Perth',
            'temp_c' => 20.5,
            'text' => 'Sunny'
        ]);
    }
}