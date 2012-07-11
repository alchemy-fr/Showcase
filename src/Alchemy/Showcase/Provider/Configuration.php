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

                if (null === $conf = json_decode(file_get_contents($configFilePath), true)) {
                    throw new \RuntimeException('Json Configuration file cannot be decoded or the encoded');
                }
                
                return new ParameterBag($conf);
            });
    }

    public function boot(Application $app)
    {

    }
}

