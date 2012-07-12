<?php

namespace Test\Provider;

use PhraseanetSDK\Tools\Entity\Manager;
use Alchemy\Showcase\Provider\EntityManagerServiceProvider;

class EntityManagerServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $app = require __DIR__ . '/../../../src/Alchemy/Showcase/App.php';
        $app->register(new EntityManagerServiceProvider(), array(
            'config.file_path' => __DIR__ . '/../../ressources/ini.json'
        ));

        $this->assertTrue(isset($app['em']));

        $this->assertTrue($app['em'] instanceof Manager);
    }

}

