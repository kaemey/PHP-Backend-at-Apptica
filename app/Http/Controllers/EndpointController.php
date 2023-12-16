<?php

namespace App\Http\Controllers;
use App\Models\Endpoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class EndpointController extends Controller
{
    const ENDPOINT = 'https://api.apptica.com/package/top_history/1421444/1';
    //
    public function index(Request $request){
        $date = $request->query('date');
        $response = Http::get(self::ENDPOINT, [
            'dateFrom' => $date, 
            'date_to' => $date,
            'B4NKGg' => 'fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l'
        ]);
        $response = json_decode($response->body(), true);
        $categories = $response['data'];

        $positions = [];
        $subpositions = [];

        foreach ($categories as $key => $category){
            $subpositions = [];
            foreach($category as $subcategory){
                $subpositions[] = min(array_filter($subcategory, function($var){return $var !== null;} ));
            }
            $positions[$key] = min($subpositions);
        }
        
        $output['status_code'] = $response['status_code'];
        $output['message'] = $response['message'];
        $output['data'] = $positions;

        Endpoint::create(['data' => json_encode($output), 'user_ip' => $request->ip()]);

        $result = json_encode($output);
        Log::info("date=$date user_ip=".$request->ip()." result=$result.");

        return $result;

    }
}
