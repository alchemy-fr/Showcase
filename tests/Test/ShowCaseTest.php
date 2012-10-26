<?php

namespace Test;

use Silex\WebTestCase;
use PhraseanetSDK\Response;
use PhraseanetSDK\EntityManager;

class ShowcaseTest extends WebTestCase
{
    private static $rollback = false;

    public function createApplication()
    {
        if (!file_exists(__DIR__ . '/../../config/config.json')) {
            copy(__DIR__ . '/../resources/ini.json', __DIR__ . '/../../config/config.json');
            self::$rollback = true;
        }

        $app = require __DIR__ . '/../../src/Alchemy/Showcase/App.php';

        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }

    public function tearDown()
    {
        if (self::$rollback) {
            unlink(__DIR__ . '/../../config/config.json');
        }

        parent::tearDown();
    }

    public function testSlash()
    {
        $client = $this->createClient();

        $responseAllFeed = new Response($this->getSampleResponse('findAll'));

        $apiClient = $this->getMock('\\PhraseanetSDK\\Client'
                , array('call')
                , array()
                , ''
                , FALSE);

        $apiClient->expects($this->any())
                ->method('call')
                ->will($this->returnValue($responseAllFeed));

        $this->app['em'] = new EntityManager($apiClient);

        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testListeEntries()
    {
        $client = $this->createClient();

        $responseOneFeed = new Response($this->getSampleResponse('findById'));
        $responseAllFeed = new Response($this->getSampleResponse('findAll'));

        $apiClient = $this->getMock('\\PhraseanetSDK\\Client'
                , array('call')
                , array()
                , ''
                , FALSE);

        $apiClient->expects($this->any())
                ->method('call')
                ->will($this->onConsecutiveCalls(
                                $responseOneFeed, $responseAllFeed
                        ));


        $this->app['em'] = new EntityManager($apiClient);

        $client->request('GET', '/feed/1');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testListeEntriesMalformedResponseException()
    {
        $this->markTestIncomplete('does not work');

        $client = $this->createClient();

        $responseOneFeed = new Response($this->getSampleResponse('badResult'));
        $responseAllFeed = new Response($this->getSampleResponse('findAll'));

        $apiClient = $this->getMock('\\PhraseanetSDK\\Client'
                , array('call')
                , array()
                , ''
                , FALSE);

        $apiClient->expects($this->any())
                ->method('call')
                ->will($this->onConsecutiveCalls(
                                $responseOneFeed, $responseAllFeed
                        ));


        $this->app['em'] = new EntityManager($apiClient);

        $client->request('GET', '/feed/1457/0/5');

        $this->assertFalse($client->getResponse()->isOk());
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testOneEntry()
    {
        $this->markTestIncomplete('does not work');

        $client = $this->createClient();

        $responseOneFeed = new Response($this->getSampleResponse('findById'));
        $responseAllFeed = new Response($this->getSampleResponse('findAll'));

        $apiClient = $this->getMock('\\PhraseanetSDK\\Client'
                , array('call')
                , array()
                , ''
                , FALSE);

        $apiClient->expects($this->any())
                ->method('call')
                ->will($this->onConsecutiveCalls(
                                $responseOneFeed, $responseAllFeed
                        ));


        $this->app['em'] = new EntityManager($apiClient);

        $client->request('GET', '/entry/1457/0/5/1661');

        $this->assertTrue($client->getResponse()->isOk());
    }

//    public function testOneEntryNotFoundExceptions()
//    {
//        $client = $this->createClient();
//
//        $responseOneFeed = new Response($this->getSampleResponse('findById'));
//        $responseAllFeed = new Response($this->getSampleResponse('findAll'));
//
//        $apiClient = $this->getMock('\\PhraseanetSDK\\Client'
//                , array('call')
//                , array()
//                , ''
//                , FALSE);
//
//        $apiClient->expects($this->any())
//                ->method('call')
//                ->will($this->onConsecutiveCalls(
//                                $responseOneFeed, $responseAllFeed
//                        ));
//
//
//        $this->app['em'] = new EntityManager($apiClient);
//
//        $client->request('GET', '/entry/1457/0/5/9999999');
//
//        $this->assertFalse($client->getResponse()->isOk());
//        $this->assertEquals(404, $client->getResponse()->getStatusCode());
//    }

    private function getSampleResponse($filename)
    {
        $filename = __DIR__ . '/../resources/feed/' . $filename . '.json';

        return json_decode(file_get_contents($filename));
    }

}

