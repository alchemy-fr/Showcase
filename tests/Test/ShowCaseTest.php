<?php

namespace Test;

use Silex\WebTestCase;
use PhraseanetSDK\Response;
use PhraseanetSDK\Tools\Entity\Manager;


class ShowcaseTest extends WebTestCase
{

    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/Alchemy/Showcase/App.php';

        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
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

        $this->app['em'] = new Manager($apiClient);

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


        $this->app['em'] = new Manager($apiClient);

        $client->request('GET', '/feed/1/0/5');

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


        $this->app['em'] = new Manager($apiClient);

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


        $this->app['em'] = new Manager($apiClient);

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
//        $this->app['em'] = new Manager($apiClient);
//
//        $client->request('GET', '/entry/1457/0/5/9999999');
//
//        $this->assertFalse($client->getResponse()->isOk());
//        $this->assertEquals(404, $client->getResponse()->getStatusCode());
//    }

    private function getSampleResponse($filename)
    {
        $filename = __DIR__ . '/../ressources/feed/' . $filename . '.json';

        return json_decode(file_get_contents($filename));
    }

}

