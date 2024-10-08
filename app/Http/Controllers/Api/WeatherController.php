<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/weather",
     *     summary="Get current weather data for Perth, Australia",
     *     tags={"Weather"},
     *     @OA\Response(
     *         response=200,
     *         description="Current weather data"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve weather data"
     *     )
     * )
     */
    public function getCurrentWeather()
    {
        try {
            $weatherData = Cache::get('current_weather_perth');
            
            if (!$weatherData) {
                $response = Http::get(env('WEATHER_API_URL'), [
                    'key' => env('WEATHER_API_KEY'),
                    'q' => 'Perth',
                    'aqi' => 'no',
                ]);

                if ($response->successful()) {
                    $weatherData = $response->json();

                    Cache::put('current_weather_perth', $weatherData, now()->addMinutes(15));
                } else {
                    return response()->json([
                        'message' => 'Failed to fetch weather data from WeatherAPI',
                        'error' => $response->body()
                    ], $response->status());
                }
            }

            return response()->json($weatherData);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the weather data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}