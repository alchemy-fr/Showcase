<?php

namespace Test;

use Alchemy\Showcase\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;

    public function setUp()
    {
        $this->logger = new \Monolog\Logger('tests');
        $this->logger->pushHandler(new \Monolog\Handler\NullHandler());
    }

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

        new Client($this->loadConfiguration(__DIR__ . '/../ressources/bad_ini.json'), $clientHttp, $this->logger);
    }

    /**
     * @covers Alchemy\Showcase\Client::__construct
     * @covers Alchemy\Showcase\Client::getAccessToken
     */
    public function testGetAccessToken()
    {
        $this->assertEquals("123456789987654321", $this->loadClient()->getAccessToken());
    }

    /**
     * @covers Alchemy\Showcase\Client::getInstanceUri
     */
    public function testGetInstanceUri()
    {
        $this->assertEquals("http://my.domain.tld", $this->loadClient()->getInstanceUri());
    }

    /**
     * @covers Alchemy\Showcase\Client::getClientId
     */
    public function testGetClientId()
    {
        $this->assertEquals("123456789", $this->loadClient()->getClientId());
    }

    /**
     * @covers Alchemy\Showcase\Client::getClientSecret
     */
    public function testGetClientSecret()
    {
        $this->assertEquals("987654321", $this->loadClient()->getClientSecret());
    }

    /**
     * @covers Alchemy\Showcase\Client::getCallbackUri
     */
    public function testGetCallbackUri()
    {
        $this->assertEquals("http://my.domain.tld/callback", $this->loadClient()->getCallbackUri());
    }

    /**
     * @covers Alchemy\Showcase\Client::getApiVersion
     */
    public function testGetApiVersion()
    {
        $this->assertEquals("1", $this->loadClient()->getApiVersion());
    }

    /**
     * @covers Alchemy\Showcase\Client::getHttpClient
     */
    public function testGetHttpClient()
    {
        $clientHttp = $this->getMock('\\Guzzle\\Http\\Client');

        $client = new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $clientHttp, $this->logger);

        $this->assertEquals($clientHttp, $client->getHttpClient());
    }

    private function loadClient()
    {
        return new Client($this->loadConfiguration(__DIR__ . '/../ressources/ini.json'), $this->getMock('\\Guzzle\\Http\\Client'), $this->logger);
    }
}
