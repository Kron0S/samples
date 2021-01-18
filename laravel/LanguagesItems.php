<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Language;
use App\Http\Resources\Language as LanguageResource;
use App\Client;

class LanguagesItems extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Client $client)
    {
        $complexity = $request->get('complexity');
        $data = LanguageResource::collection(Language::where('isShow', true)->with(['words'=>function($query) use ($complexity) {
            return $query
                ->where('isPinned', true)
                ->where('complexity', $complexity)
                ->limit(3);
        }])->get());
        $length = Language::where('isShow', true)->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'data' => $data,
                'length' => $length,
                'limit' => 0,
                'offset' => 0,
            ],
        ]);
    }
}
