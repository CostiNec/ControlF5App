<?php

namespace App\Http\Controllers;

use App\Result;
use App\Suggestion;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    const LIMIT = 30;

    public function getSuggestions(Request $request)
    {
        if (!isset($request)) {
            die();
        }

        try {
            $like = '%' . $request['input'] . '%';
            $all = Suggestion::where('keywords','like',$like)
                ->skip(self::LIMIT*(int)$request['skip'])
                ->limit(self::LIMIT)
                ->get()->toArray();
            echo json_encode($all);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    public function results(Request $request)
    {
        $results = Result::where('title','like','%'.$request['q'].'%')
                    ->orWhere('description','like','%'.$request['q'].'%')
                    ->limit(10)->get();

        return view('results',[
            'results' => $results
        ]);
    }
}
