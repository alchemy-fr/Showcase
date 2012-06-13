<?php

namespace Alchemy\Showcase;

use Alchemy\Showcase\Provider\Configuration;
use Alchemy\Showcase\Provider\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Silex\Application;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app = new Application();

$app['debug'] = false;

$app->register(new TranslationServiceProvider(), array(
    'locale_fallback' => 'en_US',
));

$app['translator.domains'] = array(
    'messages' => array(
        'en_US' => __DIR__ . '/../../../locales/en.yml',
        'de_DE' => __DIR__ . '/../../../locales/de.yml',
        'fr_FR' => __DIR__ . '/../../../locales/fr.yml',
    ),
);

$app['monolog'] = $app->share(function() use ($app) {

        $logger = new \Monolog\Logger('Showcase');

        if ($app['debug'] === true) {
            $logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__ . '/../../../queries.log', 1));
        } else {
            $logger->pushHandler(new \Monolog\Handler\NullHandler());
        }
        
        return $logger;
    });

$app['translator.loader'] = $app->share(function () {
        return new YamlFileLoader();
    });

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../../../templates',
    'cache'     => __DIR__ . '/../../../cache/',
));

$app->register(new Configuration(), array(
    'config.file_path' => __DIR__ . '/../../../config/myini.json',
));

$app->register(new EntityManager());


$app->before(function () use ($app) {
        $app['locale'] = $app['configuration']->get('locale');
    });

$app->get('/', function(Application $app) {
        $feedCollection = $app['em']->getRepository('feed')->findAll();

        $templateDatas = array(
            'feeds'         => $feedCollection,
            'configuration' => $app['configuration'],
        );

        return $app['twig']->render('index.html.twig', $templateDatas);
    });

$app->get('/feed/{feedId}/{offset}/{perPage}', function(Application $app, $feedId, $offset, $perPage) {
            $feed = $app['em']->getRepository('feed')->findById($feedId, $offset, $perPage);

            $feedCollection = $app['em']->getRepository('feed')->findAll();

            $templateDatas = array(
                'feed'  => $feed
                , 'feeds' => $feedCollection
            );

            return $app['twig']->render('feed.html.twig', $templateDatas);
        })
    ->assert('feedId', '\d+')
    ->assert('offset', '\d+')
    ->assert('perPage', '\d+');

$app->get('/entry/{feedId}/{offset}/{perPage}/{entryId}', function(Application $app, $feedId, $offset, $perPage, $entryId) {
            $feed = $app['em']->getRepository('feed')->findById($feedId, $offset, $perPage);

            $entries = $feed->getEntries();

            foreach ($entries as $entry) {
                if ($entry->getId() == $entryId) {
                    $feedCollection = $app['em']->getRepository('feed')->findAll();

                    $templateDatas = array(
                        'entry' => $entry
                        , 'feed'  => $feed
                        , 'feeds' => $feedCollection
                    );

                    return $app['twig']->render('entry.html.twig', $templateDatas);
                }
            }

            throw new NotFoundHttpException();
        })
    ->assert('feedId', '\d+')
    ->assert('offset', '\d+')
    ->assert('perPage', '\d+')
    ->assert('entryId', '\d+');

//$app->error(function($e, $code) use ($app)
//        {
//            switch ($code)
//            {
//                case 404:
//                    $message = 'The requested page could not be found.';
//                    break;
//                default:
//                    $message = 'We are sorry, but something went terribly wrong.';
//                    $code = 500;
//            }
//
//            return new Response($message, $code);
//        });

return $app;
