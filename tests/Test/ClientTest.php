<?php

namespace Test;

use Alchemy\Showcase\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    protected function loadConfiguration($path)
    {
        return new ParameterBag(json_decode(file_get_contents($path), true));
    }

    /**
     * @covers Alchemy\Showcase\Client::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        new Client($this->loadConfiguration(__DIR__ . '/../ressources/bad_ini.json'), $clientHttp);
    }

    /**
     * @covers Alchemy\Showcase\Client::__construct
     * @covers Alchemy\Showcase\Client::getAccessToken
     */
    public function testGetAccessToken()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp);
        $this->assertEquals("123456789987654321", $client->getAccessToken());
    }

    /**
     * @covers Alchemy\Showcase\Client::getInstanceUri
     */
    public function testGetInstanceUri()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp);
        $this->assertEquals("http://my.domain.tld", $client->getInstanceUri());
    }

    /**
     * @covers Alchemy\Showcase\Client::getClientId
     */
    public function testGetClientId()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp);
        $this->assertEquals("123456789", $client->getClientId());
    }

    /**
     * @covers Alchemy\Showcase\Client::getClientSecret
     */
    public function testGetClientSecret()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp);
        $this->assertEquals("987654321", $client->getClientSecret());
    }

    /**
     * @covers Alchemy\Showcase\Client::getCallbackUri
     */
    public function testGetCallbackUri()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp);
        $this->assertEquals("http://my.domain.tld/callback", $client->getCallbackUri());
    }

    /**
     * @covers Alchemy\Showcase\Client::getApiVersion
     */
    public function testGetApiVersion()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp);
        $this->assertEquals("1", $client->getApiVersion());
    }

    /**
     * @covers Alchemy\Showcase\Client::getHttpClient
     */
    public function testGetHttpClient()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp);

        $this->assertEquals($clientHttp, $client->getHttpClient());
    }
}
