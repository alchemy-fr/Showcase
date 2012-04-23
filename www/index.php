<?php

require_once __DIR__ . '/../bootstrap.php';

use Silex;
use Alchemy;
use Doctrine\Common\Collections\ArrayCollection;

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../templates',
    'twig.class_path' => __DIR__ . '/../vendor/twig/twig/lib'
));

$app->register(new Alchemy\Showcase\Provider\EntityManager(), array(
    'config.file_path' => __DIR__ . '/../config/ini.json'
));

$app->get('/', function(Silex\Application $app)
        {
            $feedCollection = $app['em']->getRepository('feed')->findAll();

            $templateDatas = array('feeds' => $feedCollection);

            return $app['twig']->render('index.html.twig', $templateDatas);
        });

$app->get('/feed/{feedId}/{offset}/{perPage}', function(Silex\Application $app, $feedId, $offset, $perPage)
                {
                    $feed = $app['em']->getRepository('feed')->findById($feedId, $offset, $perPage);

                    $templateDatas = array('feed' => $feed);

                    return $app['twig']->render('feed.html.twig', $templateDatas);
                })
        ->assert('feedId', '\d+')
        ->assert('offset', '\d+')
        ->assert('perPage', '\d+');

$app->get('/entry/{feedId}/{offset}/{perPage}/{entryId}', function(Silex\Application $app, $feedId, $offset, $perPage, $entryId)
                {
                    $feed = $app['em']->getRepository('feed')->findById($feedId, $offset, $perPage);

                    $entries = $feed->getEntries();

                    $entriesCollection = new ArrayCollection();

                    foreach ($entries as $entry)
                    {
                        if ($entry->getId() !== $entryId)
                        {
                            continue;
                        }

                        $entriesCollection->add($entry);

                        break;
                    }

                    $feed->setEntries($entriesCollection);

                    $templateDatas = array('feed' => $feed);

                    return $app['twig']->render('entry.html.twig', $templateDatas);
                })
        ->assert('feedId', '\d+')
        ->assert('offset', '\d+')
        ->assert('perPage', '\d+')
        ->assert('entryId', '\d+');

$app->error(function($e, $code) use ($app)
        {
            switch ($code)
            {
                case 404:
                    $message = 'The requested page could not be found.';
                    break;
                default:
                    $message = 'We are sorry, but something went terribly wrong.';
                    $code = 500;
            }

            return new Response($message, $code);
        });

$app->run();


