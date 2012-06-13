<?php

namespace Alchemy\Showcase;

use Alchemy\Showcase\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use PhraseanetSDK;
use Guzzle\Http\Client as HttpClient;

class Client extends PhraseanetSDK\Client
{

    protected $instanceUri;
    protected $clientId;
    protected $clientSecret;
    protected $callbackUri;
    protected $apiVersion;
    protected $devToken;

    public function __construct(ParameterBag $configuration, HttpClient $clientHttp, \Monolog\Logger $logger)
    {
        try
        {
            $this->instanceUri = rtrim($configuration->get('instance_uri'), '/');
            $this->clientId = $configuration->get('client_id');
            $this->clientSecret = $configuration->get('client_secret');
            $this->callbackUri = $configuration->get('callback_uri');
            $this->apiVersion = $configuration->get('api_version');
            $this->devToken = $configuration->get('dev_token');
        }
        catch (ParameterNotFoundException $e)
        {
            throw new \InvalidArgumentException(sprintf(
                            'Bad configuration missing key %s', $e->getKey()
                    ),
                    0,
                    $e
            );
        }

        $baseUrl = sprintf('%s/api/v{{version}}', $this->instanceUri);

        $clientHttp->setBaseUrl($baseUrl);

        $clientHttp->setConfig(array('version' => $this->apiVersion));

        parent::__construct(
                $this->instanceUri
                , $this->clientId
                , $this->clientSecret
                , $clientHttp
                , $logger
        );
    }

    public function getAccessToken()
    {
        return $this->devToken;
    }

    public function getInstanceUri()
    {
        return $this->instanceUri;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getCallbackUri()
    {
        return $this->callbackUri;
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    public function getHttpClient()
    {
        return $this->httpClient;
    }
}

