<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;

class SearchController extends Controller
{
    public function index(Request $request, Tweet $tweet){
        $user = auth()->user();
        $keyword = $request->input('keyword');

        if(!empty($keyword)){
            $tweets_data = $tweet->tweetSearch($keyword);
            return view('search.index', [
                'user' => $user,
                'tweets_data' => $tweets_data,
                'keyword' => $keyword
            ]);
        }

        return view('search.index', [
            'user' => $user,
            'tweets_data' => [],
            'keyword' => '検索ワード'
        ]);
    }
}
