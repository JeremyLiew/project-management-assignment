<?php
// Jeremy
namespace App\Http\Controllers;

use App\Decorators\AboutUsLogDecorator;
use App\Http\Requests\AboutUs\GetMembersRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AboutUsController extends Controller
{
    private $apiUrl = 'http://localhost:8082/api/members';

    public function index(Request $request)
    {
        $response = $this->fetchAboutUsData();
        $logDecorator = new AboutUsLogDecorator(null, $request);

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
    }

    public function getMembersViaWebService(GetMembersRequest $request)
    {
        $validatedData = $request->validated();
        $query = strtolower($validatedData['query']);

        $response = $this->fetchFilteredMembers($query);
        $logDecorator = new AboutUsLogDecorator(null,$request);

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
        return view('aboutus.index', ['error' => $errorMessage]);
    }
}
