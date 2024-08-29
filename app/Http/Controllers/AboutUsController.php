<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AboutUsController extends Controller {

    public function index(){
        return view('aboutus.index');
    }

    public function getMembersViaWebService(Request $request){
        $id = $request->input('id');

        $response = Http::get('http://127.0.0.1:8080/ProjManagementApi/api.php', [
            'id' => $id
        ]);

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status'] == 200) {
                return view('aboutus.index', ['member' => $result['data']]);
            } else {
                return view('aboutus.index', ['error' => $result['status_message']]);
            }
        } else {
            return view('aboutus.index', ['error' => 'Failed to fetch data from API.']);
        }
    }
}
