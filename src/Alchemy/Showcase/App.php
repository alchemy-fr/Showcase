<?php

namespace Alchemy\Showcase;

use Alchemy\Phrasea\SDK\PhraseanetSDKServiceProvider;
use Alchemy\Showcase\Provider\Configuration;
use Alchemy\Showcase\Provider\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Guzzle\GuzzleServiceProvider;
use Monolog\Logger;
use Silex\Application;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app = new Application();

$app['debug'] = true;

if ($app['debug'] == false) {
    ini_set('display_errors', 'off');
} else {
    ini_set('display_errors', 'on');
}

$app->register(new Configuration(), array(
    'config.file_path' => __DIR__ . '/../../../config/myini.json',
));

$app->register(new TranslationServiceProvider(), array(
    'locale_fallback' => 'en_US',
));

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());

            $translator->addResource('yaml', __DIR__ . '/../../../locales/en.yml', 'en_US');
            $translator->addResource('yaml', __DIR__ . '/../../../locales/de.yml', 'de_DE');
            $translator->addResource('yaml', __DIR__ . '/../../../locales/fr.yml', 'fr_FR');

            return $translator;
        }));

$app->register(new \Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../../../queries.log',
    'monolog.level'   => $app['debug'] ? Logger::DEBUG : Logger::ALERT,
));

$app->register(new PhraseanetSDKServiceProvider(), array(
    'phraseanet-sdk.apiUrl'      => $app['configuration']->get('instance_uri'),
    'phraseanet-sdk.apiKey'      => $app['configuration']->get('client_id'),
    'phraseanet-sdk.apiSecret'   => $app['configuration']->get('client_secret'),
    'phraseanet-sdk.apiDevToken' => $app['configuration']->get('dev_token'),
    'phraseanet-sdk.cache'       => $app['configuration']->get('cache'),
    'phraseanet-sdk.cache_host'  => $app['configuration']->get('cache_host'),
    'phraseanet-sdk.cache_port'  => $app['configuration']->get('cache_port'),
));

$app->register(new GuzzleServiceProvider());

$app->register(new TwigServiceProvider(), array(
    'twig.path'    => __DIR__ . '/../../../templates',
    'twig.options' => array('cache' => __DIR__ . '/../../../cache/'),
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

$app->get('/feed/{feedId}', function(Application $app, Request $request, $feedId) {
            $feed = $app['em']->getRepository('feed')->findById($feedId, $request->get('offset', 0), $request->get('perPage', 10));

            return $app['twig']->render('feed.html.twig', array('feed'  => $feed));
        })
    ->assert('feedId', '\d+');

$app->get('/entry/{entryId}', function(Application $app, $entryId) {
            $entry = $app['em']->getRepository('entry')->findById($entryId);

            return $app['twig']->render('entry.html.twig', array('entry' => $entry));
        })
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
