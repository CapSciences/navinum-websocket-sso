<?php
namespace CapSciences\ServerVip\NavinumBundle\Providers;

use Guzzle\Http\Client;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class ApiProvider
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    private $webserviceUrl;
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer, $url, LoggerInterface $logger)
    {
        $this->webserviceUrl = $url;
        $this->serializer    = $serializer;
        $this->logger        = $logger;
    }

    public function setWebserviceUrl($url)
    {
        $this->webserviceUrl = $url;
    }

    protected function get($path, $modelClass = null)
    {
        $url = $this->webserviceUrl . $path;


        $client = new Client($this->webserviceUrl);

        $request = $client->get($path, null);
        try {
            $response = $request->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
        }

        $this->logger->debug($url . " - " . $response->getStatusCode() . "\n");

        if (!$modelClass) {
            $modelClass = $this->getModelClass();
        }

        switch ($response->getStatusCode()) {
            case 200:

                if ($modelClass) {
                    return $this->serializer->deserialize($response->getBody(), $modelClass, 'json');
                } else {
                    return json_decode($response->getBody());
                }
            case 404:
                return null;
            case 403:
                throw new \Exception("API denied access", 403);
            default:
                throw new \Exception($response->getBody(), $response->getStatusCode());
        }
    }

    protected function getModelClass()
    {
        return null;
    }

    protected function post($path, $fields, $modelClass = null)
    {
        $url = $this->webserviceUrl . $path;

        $client = new Client($this->webserviceUrl);

        $request = $client->post($path, null, json_encode($fields));
        try {
            $response = $request->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $ex) {
            $response = $ex->getResponse();
        }
        $this->logger->debug($url . " - " . $response->getStatusCode());

        if (!$modelClass) {
            $modelClass = $this->getModelClass();
        }

        $this->logger->debug($response->getBody());


        switch ($response->getStatusCode()) {
            case 200:
                if ($modelClass) {
                    return $this->serializer->deserialize($response->getBody(), $modelClass, 'json');
                } else {
                    return json_decode($response->getBody());
                }
            case 404:
                return null;
            case 403:
                throw new \Exception("API denied access", 403);
            case 406:
                return json_decode($response->getBody());
            default:
                throw new \Exception($response->getBody(), $response->getStatusCode());
        }
    }

}