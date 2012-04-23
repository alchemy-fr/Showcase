<?php

namespace Alchemy\Showcase\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Alchemy\Showcase\Client as ApiClient;
use PhraseanetSDK\Tools\Entity\Manager;

class EntityManager implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['httpClient'] = new \Guzzle\Http\Client();



        $app['apiClient'] = $app->share(function () use ($app)
                {
                    $default = __DIR__ . '/../../../../config/ini.json';
                    
                    $configFilePath = isset($app['config.file_path']) ?
                            $app['config.file_path'] : $default;
                    
                    return new ApiClient($configFilePath, $app['httpClient']);
                });

        $app['em'] = $app->share(function () use ($app)
                {
                    return new Manager($app['apiClient']);
                });
    }

}

