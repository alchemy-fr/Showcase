<?php

namespace Alchemy\Showcase\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use PhraseanetSDK\EntityManager;

class EntityManagerServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['em'] = $app->share(function () use ($app)
                {
                    return new EntityManager($app['phraseanet-sdk']);
                });
    }

    public function boot(Application $app)
    {

    }
}

