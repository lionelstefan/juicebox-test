<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateWeatherData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            $response = Http::get(env('WEATHER_API_URL'), [
                'key' => env('WEATHER_API_KEY'),
                'q' => 'Perth',
                'aqi' => 'no',
            ]);

            if ($response->successful()) {
                $weatherData = $response->json();

                Cache::put('current_weather_perth', $weatherData, now()->addMinutes(15));
            }
        } catch (\Exception $e) {
            Log::error('Failed to update weather data: ' . $e->getMessage());
        }
    }
}