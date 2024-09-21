<?php

// Jeremy

namespace App\Http\Controllers;

use App\Decorators\AboutUsLogDecorator;
use App\Http\Requests\AboutUs\GetMembersRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class AboutUsController extends Controller
{
    private $apiUrl = 'http://localhost:8082/api/about-us';

    public function index(Request $request)
    {
        $logDecorator = new AboutUsLogDecorator($request);
        try {
            $response = $this->fetchAboutUsData();

            if ($response->successful()) {
                $result = $response->json();
                $logDecorator->logAction('Fetched About Us Data', ['status' => $result['status']]);

                if ($result['status'] == 200) {
                    return $this->renderView($result['data']);
                } else {
                    $logDecorator->logAction('Fetch Error', ['status_message' => $result['status_message']]);
                    return $this->renderErrorView($result['status_message']);
                }
            } else {
                $logDecorator->logAction('Fetch Failure', ['error' => 'Failed to fetch data from API.']);
                return $this->renderErrorView('Failed to fetch data from API.');
            }
        } catch (ConnectionException $e) {
            // Log the connection error and redirect to an error view.
            $logDecorator->logAction('Connection Error', ['error' => $e->getMessage()]);
            return $this->renderServiceUnavailableView();
        }
    }

    public function getMembersViaWebService(GetMembersRequest $request)
    {
        $validatedData = $request->validated();
        $query = strtolower($validatedData['query']);

        $response = $this->fetchFilteredMembers($query);
        $logDecorator = new AboutUsLogDecorator($request);

        if ($response->successful()) {
            $result = $response->json();

            $logDecorator->logAction('Search Members', ['query' => $query, 'status' => $result['status']]);

            if ($result['status'] == 200) {
                $members = $result['data']['members'];
                $aboutUsContent = $result['data']['about_us'];

                return view('aboutus.index', [
                    'members' => $members,
                    'aboutUsContent' => $aboutUsContent
                ]);
            } else {
                $logDecorator->logAction('Search Error', ['status_message' => $result['status_message']]);
                return $this->renderErrorView($result['status_message']);
            }
        } else {
            $logDecorator->logAction('Search Failure', ['error' => 'Failed to fetch data from API.']);
            return $this->renderErrorView('Failed to fetch data from API.');
        }
    }

    private function fetchAboutUsData()
    {
        return Http::get($this->apiUrl);
    }

    private function fetchFilteredMembers($query)
    {
        $url = $this->apiUrl . '?query=' . urlencode($query);
        return Http::get($url);
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
        return view('errors.service_unavailable', ['message' => $errorMessage]);
    }

    private function renderServiceUnavailableView()
    {
        return view('errors.service_unavailable', [
            'message' => 'The About Us service is currently unavailable. Please try again later.'
        ]);
    }
}
