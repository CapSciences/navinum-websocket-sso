<?php
namespace CapSciences\ServerVip\WebsocketBundle\Providers;

use Guzzle\Http\Client;

class ApiProvider
{

    private $webserviceUrl;

    public function setWebserviceUrl($url)
    {
        $this->webserviceUrl = $url;
    }

    protected function get($path)
    {
        $url = $this->webserviceUrl . $path;
        if (php_sapi_name() == 'cli') {
            echo $url;
        }

        $client = new Client($this->webserviceUrl);

        $request = $client->get($path, null);
        try {
            $response = $request->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
        }

        if (php_sapi_name() == 'cli') {
            echo " - " . $response->getStatusCode() . "\n";
        }


        switch ($response->getStatusCode()) {
            case 200:
                return json_decode($response->getBody());
            case 404:
                return null;
            case 403:
                throw new \Exception("API denied access", 403);
            default:
                throw new \Exception($response->getBody(), $response->getStatusCode());
        }
    }

    protected function post($path, $fields)
    {
        $url = $this->webserviceUrl . $path;
        if (php_sapi_name() == 'cli') {
            echo $url;
        }

        $client = new Client($this->webserviceUrl);

        $request = $client->post($path, null, json_encode($fields));
        try {
            $response = $request->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
        }

        if (php_sapi_name() == 'cli') {
            echo " - " . $response->getStatusCode() . "\n";
        }

        switch ($response->getStatusCode()) {
            case 200:
                return json_decode($response->getBody());
            case 404:
                return null;
            case 403:
                throw new \Exception("API denied access", 403);
            default:
                throw new \Exception($response->getBody(), $response->getStatusCode());
        }
    }

}