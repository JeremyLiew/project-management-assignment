<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class LanguageController extends Controller {

    public function changeLanguage($language) {
        $response = Http::get("http://localhost:8080/language/page/{$language}");
        return response($response->body())->header('Content-Type', 'text/html');
    }
}
