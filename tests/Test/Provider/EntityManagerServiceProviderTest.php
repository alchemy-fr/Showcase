<?php

namespace Test\Provider;

use PhraseanetSDK\EntityManager;
use Alchemy\Showcase\Provider\EntityManagerServiceProvider;

class EntityManagerServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $app = require __DIR__ . '/../../../src/Alchemy/Showcase/App.php';
        $app->register(new EntityManagerServiceProvider(), array(
            'config.file_path' => __DIR__ . '/../../resources/ini.json'
        ));

        $this->assertTrue(isset($app['em']));

        $this->assertTrue($app['em'] instanceof EntityManager);
    }

}

