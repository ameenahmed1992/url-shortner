<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\UrlShortnerService;

class UrlShortnerController extends Controller
{
    protected $urlShortnerService;
    public function __construct(UrlShortnerService $urlShortnerService)
    {
        $this->urlShortnerService = $urlShortnerService;
    }

    public function generateOrGetShortUrl(Request $request)
    {

        Log::info(__METHOD__);

        try {
            $longUrl = $request->input('long_url');

            $shortUrlCode = $this->urlShortnerService->generateOrGetShortUrl($longUrl);

            $data = [
                'short_url' => config('app.url') . '/short/' . $shortUrlCode,
            ];

            $response = [
                'has_error' => false,
                'message' => 'short Url retrieved succesfully.',
                'data' => $data
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $response = [
                'has_error' => true,
                'message' => $th->getMessage(),
            ];
        }
        Log::info(__METHOD__, ['response' => $response]);
        return $response;
    }

    public function getLongUrlAndViewsFromShortUrl($shortUrlCode)
    {
        Log::info(__METHOD__);

        try {

            $longUrl = $this->urlShortnerService->getLongUrlFromUrlCode($shortUrlCode);
            $viewCounts = $this->urlShortnerService->getViewCountsFromUrlCode($shortUrlCode);

            $data = [
                'long_url' => $longUrl,
                'view_counts' => $viewCounts
            ];

            $response = [
                'has_error' => false,
                'message' => 'Url details retrieved succesfully.',
                'data' => $data
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $response = [
                'has_error' => true,
                'message' => $th->getMessage(),
            ];
        }
        Log::info(__METHOD__, ['response' => $response]);
        return $response;
    }
}
