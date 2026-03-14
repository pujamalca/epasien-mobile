<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Support\Facades\Http;

class RadiologyProxyController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function image(string $filename)
    {
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($filename));
        if (!$filename) abort(422);

        $url = $this->api->getBaseUrl() . '/epasien/api/v2/radiologi-foto.php?file=' . urlencode($filename);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['X-Session-Id' => $this->session->getSessionId()])
                ->get($url);

            if ($response->status() === 404) abort(404);
            if ($response->status() === 415) {
                return response()->json(['error' => 'Format tidak didukung, hubungi RS'], 415);
            }

            $contentType = $response->header('Content-Type') ?? 'image/jpeg';
            return response($response->body(), 200)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'private, max-age=300');
        } catch (\Exception $e) {
            abort(503);
        }
    }
}
