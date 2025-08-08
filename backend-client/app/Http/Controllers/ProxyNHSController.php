<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProxyNHSController extends Controller
{
    /**
     * Proxy toutes les requêtes NHS vers le backend-nhs
     */
    public function handle(Request $request, $any = '')
    {
        $nhsBaseUrl = env('NHS_API_URL', 'http://localhost:8001/');
        $nhsUrl = rtrim($nhsBaseUrl, '/') . '/api/nhs/' . ltrim($any, '/');
        
        $method = $request->method();
        $headers = $request->header();
        $body = $request->getContent();

        Log::info('Proxy NHS Request', [
            'url' => $nhsUrl, 
            'method' => $method,
            'original_path' => $any
        ]);

        try {
            // Proxy la requête vers backend-nhs
            $response = Http::withHeaders($headers)->send($method, $nhsUrl, [
                'body' => $body,
            ]);

            Log::info('Proxy NHS Response', [
                'status' => $response->status(),
                'url' => $nhsUrl
            ]);

            return response($response->body(), $response->status())
                ->withHeaders($response->headers());
                
        } catch (\Exception $e) {
            Log::error('Proxy NHS Error', [
                'error' => $e->getMessage(),
                'url' => $nhsUrl
            ]);
            
            return response()->json([
                'error' => 'NHS Backend unavailable',
                'message' => $e->getMessage()
            ], 503);
        }
    }
}
