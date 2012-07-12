<?php

namespace Alchemy\Showcase\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use PhraseanetSDK\Tools\Entity\Manager;

class EntityManagerServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['em'] = $app->share(function () use ($app)
                {
                    return new Manager($app['phraseanet-sdk']);
                });
    }

    public function boot(Application $app)
    {

    }
}

