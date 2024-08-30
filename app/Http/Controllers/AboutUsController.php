<?php

namespace App\Http\Controllers;

use App\Http\Requests\AboutUs\GetMembersRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AboutUsController extends Controller
{
    private $apiUrl = 'http://localhost:8081/api';

    public function index()
    {
        $response = $this->fetchApiData();

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status'] == 200) {
                return $this->renderView($result['data']);
            } else {
                return $this->renderErrorView($result['status_message']);
            }
        } else {
            return $this->renderErrorView('Failed to fetch data from API.');
        }
    }

    public function getMembersViaWebService(GetMembersRequest $request)
    {
        $validator = $request->validated();
        $query = strtolower($validator['query']);
        $response = $this->fetchApiData();

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status'] == 200) {
                $members = $result['data']['members'];
                $aboutUsContent = $result['data']['about_us'];

                $filteredMembers = $this->filterMembers($members, $query);

                return view('aboutus.index', [
                    'members' => $filteredMembers,
                    'aboutUsContent' => $aboutUsContent
                ]);
            } else {
                return $this->renderErrorView($result['status_message']);
            }
        } else {
            return $this->renderErrorView('Failed to fetch data from API.');
        }
    }

    private function fetchApiData()
    {
        return Http::get($this->apiUrl);
    }

    private function renderView($data)
    {
        return view('aboutus.index', [
            'members' => $data['members'],
            'aboutUsContent' => $data['about_us']
        ]);
    }

    private function renderErrorView($errorMessage)
    {
        return view('aboutus.index', ['error' => $errorMessage]);
    }

    private function filterMembers($members, $query)
    {
        if ($query) {
            return array_filter($members, function ($member) use ($query) {
                $nameMatch = stripos(strtolower($member['name']), $query) !== false;
                $skillsMatch = array_filter(array_map('strtolower', $member['skills']), function ($skill) use ($query) {
                    return stripos($skill, $query) !== false;
                });

                return $nameMatch || !empty($skillsMatch);
            });
        }

        return $members;
    }
}
