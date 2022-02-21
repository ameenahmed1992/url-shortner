<?php

namespace App\Services;

use Exception;
use App\Models\UrlShortner;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UrlShortnerService
{

    public function generateOrGetShortUrl($longUrl)
    {
        Log::info(__METHOD__, [get_defined_vars()]);

        DB::beginTransaction();
        try {

            // check if same url exists or create new.
            $urlShortner = UrlShortner::firstOrCreate(
                ['long_url' => $longUrl],
                ['short_url_code' => Str::random(10)]
            );

            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollback();
            throw $th;
        }
        Log::info(__METHOD__, ['short_url_code' => $urlShortner->short_url_code]);

        return $urlShortner->short_url_code;
    }

    public function getLongUrlFromUrlCode($urlCode)
    {
        Log::info(__METHOD__, [get_defined_vars()]);

        DB::beginTransaction();
        try {
            $urlShortnerObject = UrlShortner::where('short_url_code', $urlCode)->first();

            if (empty($urlShortnerObject)) {
                throw new Exception('Url not found');
            }

            $urlShortnerObject->visits = $urlShortnerObject->visits + 1;
            $urlShortnerObject->save();

            $longUrl = $urlShortnerObject->long_url;

            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            throw $th;
        }

        Log::info(__METHOD__, ['longUrl' => $longUrl]);

        return $longUrl;
    }

    public function getViewCountsFromUrlCode($urlCode)
    {
        Log::info(__METHOD__, [get_defined_vars()]);

        try {
            $urlShortnerObject = UrlShortner::where('short_url_code', $urlCode)->first();

            if (empty($urlShortnerObject)) {
                throw new Exception('Url not found');
            }

            $viewCounts = $urlShortnerObject->visits;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            DB::rollback();
            throw $th;
        }

        Log::info(__METHOD__, ['viewCounts' => $viewCounts]);

        return $viewCounts;
    }
}
