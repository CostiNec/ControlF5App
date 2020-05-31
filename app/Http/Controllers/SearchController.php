<?php

namespace App\Http\Controllers;

use App\Result;
use App\Suggestion;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    const LIMIT = 30;
    const LIMIT_RESULT = 10;

    public function getSuggestions(Request $request)
    {
        $like = '%' . $request['input'] . '%';
        $all = Suggestion::where('keywords','like',$like)
            ->skip(self::LIMIT*(int)$request['skip'])
            ->limit(self::LIMIT)
            ->get()->toArray();
        echo json_encode($all);
    }

    public function getResults(Request $request)
    {
        $results = Result::where('title','like','%'.$request['q'].'%')
            ->orWhere('description','like','%'.$request['q'].'%')
            ->skip((int)$request['skip']*self::LIMIT_RESULT)
            ->limit(self::LIMIT_RESULT + 1)->get()->toArray();

        echo json_encode($results);
    }

    public function results(Request $request)
    {
        $results = Result::where('title','like','%'.$request['q'].'%')
                    ->orWhere('description','like','%'.$request['q'].'%')
                    ->limit(self::LIMIT_RESULT + 1)->get();

        $count = count($results);
        return view('results',[
            'results' => $results,
            'count' => $count
        ]);
    }
}
