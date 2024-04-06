<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Ariaieboy\LaravelSafeBrowsing\Facades\LaravelSafeBrowsing;

use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    private function checkUrl(Request $request)
    {
        $url = $request->input('url');
        $response = [
            'url' => $url,
            'safe' => false,
            'error' => null,
        ];
        try {
            $response['safe'] = LaravelSafeBrowsing::isSafeUrl($url, true);
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
        }
        
        return $response['safe'];
    }

    private function urlExists($url)
    {
        $url = DB::table('urls')->where('url', $url)->first();
        return $url;
    }

    public function store(Request $request)
    {
        $url = $request->input('url');
        
        if($this->urlExists($url)){
            $hash = $this->urlExists($url)->hash;
            $response = [
                'url' => $url,
                'hash' => $hash,
                'error' => null,
            ];
            return response()->json($response, 200);
        }

        if($this->checkUrl($request)){
            $hash = md5($url);
            $hash = substr($hash, 0, 6);
            $response = [
                'url' => $url,
                'hash' => $hash,
                'error' => null,
            ];
            DB::table('urls')->insert([
                'url' => $url,
                'hash' => $hash,
            ]);
        } else {
            $response = [
                'url' => $url,
                'hash' => null,
                'error' => 'URL is not safe',
            ];
        }

        if($response['error']){
            return response()->json($response, 400);
        }

        return response()->json($response, 200);
    }

    public function getUrls()
    {
        $urls = DB::table('urls')->get();
        return response()->json($urls);
    }
    
}
