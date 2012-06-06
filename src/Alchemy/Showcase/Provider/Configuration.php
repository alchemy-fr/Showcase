<?php

namespace Alchemy\Showcase\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class Configuration implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['configuration'] = $app->share(function () use ($app) {
                $default = __DIR__ . '/../../../../config/ini.json';

                $configFilePath = isset($app['config.file_path']) ? $app['config.file_path'] : $default;

                return new ParameterBag(json_decode(file_get_contents($configFilePath), true));
            });
    }
    
    public function boot(Application $app)
    {
        
    }
}

