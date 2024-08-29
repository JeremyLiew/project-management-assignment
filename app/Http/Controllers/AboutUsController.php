<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AboutUsController extends Controller
{
    public function index()
    {
        $response = Http::get('http://127.0.0.1:8080/ProjManagementApi/api.php');

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status'] == 200) {
                $members = $result['data']['members'];
                $aboutUsContent = $result['data']['about_us'];

                return view('aboutus.index', ['members' => $members, 'aboutUsContent' => $aboutUsContent]);
            } else {
                return view('aboutus.index', ['error' => $result['status_message']]);
            }
        } else {
            return view('aboutus.index', ['error' => 'Failed to fetch data from API.']);
        }
    }

    public function getMembersViaWebService(Request $request)
    {
        $query = strtolower($request->input('query', ''));

        $response = Http::get('http://127.0.0.1:8080/ProjManagementApi/api.php');

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status'] == 200) {
                $members = $result['data']['members'];
                $aboutUsContent = $result['data']['about_us'];

                if ($query) {
                    $filteredMembers = array_filter($members, function ($member) use ($query) {
                        $nameMatch = stripos(strtolower($member['name']), $query) !== false;
                        $skillsMatch = array_filter(array_map('strtolower', $member['skills']), function ($skill) use ($query) {
                            return stripos($skill, $query) !== false;
                        });

                        return $nameMatch || !empty($skillsMatch);
                    });
                } else {
                    $filteredMembers = $members;
                }

                return view('aboutus.index', ['members' => $filteredMembers, 'aboutUsContent' => $aboutUsContent]);
            } else {
                return view('aboutus.index', ['error' => $result['status_message']]);
            }
        } else {
            return view('aboutus.index', ['error' => 'Failed to fetch data from API.']);
        }
    }
}
