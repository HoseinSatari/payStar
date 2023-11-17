<?php

namespace App\Services\ApiRequest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ApiRequestService
{
    protected $client;
    protected $headers;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('app.api_url'),
            'timeout' => 15,
        ]);


        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function get($url, $params = [], $headers = [])
    {
        return $this->sendRequest('GET', $url, $params, $headers);
    }

    public function post($url, $params = [], $headers = [])
    {
        return $this->sendRequest('POST', $url, $params, $headers);
    }

    public function put($url, $params = [], $headers = [])
    {
        return $this->sendRequest('PUT', $url, $params, $headers);
    }

    public function delete($url, $params = [], $headers = [])
    {
        return $this->sendRequest('DELETE', $url, $params, $headers);
    }

    protected function sendRequest($method, $url, $params = [], $headers = [])
    {
        $headers = array_merge($this->headers, $headers);


        // Check if token is available in session
//        $token = session('jwt_token');
//        if ($token) {
//            $headers['Authorization'] = 'Bearer ' . $token;
//        }

        try {
            $response = $this->client->request($method, $url, [
                'headers' => $headers,
                'json' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $base = config('app.api_url');
            Log::error("{$response->getStatusCode()} - {$base}/{$url}");
            return json_decode($response->getBody()->getContents() , true);
        }
    }

    public function postWithFile($url, $params = [], $fileField = 'file', $headers = [])
    {
        $headers = array_merge($this->headers, $headers);
        unset($headers['Content-Type']);

        // Check if token is available in session
        $token = session('jwt_token');
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        try {

            $response = $this->client->request('POST', $url, [
                'headers' => $headers,
                'multipart' => [
                    [
                        'name' => $fileField,
                        'contents' => fopen($params[$fileField]->getRealPath(), 'r'),
                    ],
                ],
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            $response = $e->getResponse();

            return json_decode($response->getBody()->getContents());
        }
    }
}
