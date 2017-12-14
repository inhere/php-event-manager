<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-07
 * Time: 14:07
 */

use Inhere\Event\EventManager;

require dirname(__DIR__) . '/tests/boot.php';

$em = new EventManager();

// register a group listener
$em->addListener(new \Inhere\Event\Examples\EnumGroupListener());

$demo = new class
{
    use \Inhere\Event\EventManagerAwareTrait;

    public function run()
    {
        $this->eventManager->trigger(\Inhere\Event\Examples\EnumGroupListener::TEST_EVENT);

        sleep(1);

        $this->eventManager->trigger(\Inhere\Event\Examples\EnumGroupListener::POST_EVENT);
    }
};

$demo->setEventManager($em);
$demo->run();
