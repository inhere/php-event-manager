<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-07
 * Time: 14:07
 */

use Inhere\Event\EventManager;
use Inhere\Event\Examples\EnumGroupListener;

require dirname(__DIR__) . '/tests/boot.php';

$em = new EventManager();

// register a group listener
$em->addListener(new EnumGroupListener());

$demo = new class
{
    use \Inhere\Event\EventManagerAwareTrait;

    public function run()
    {
        $this->eventManager->trigger(EnumGroupListener::TEST_EVENT);

        echo '.';
        sleep(1);
        echo ".\n";

        $this->eventManager->trigger(EnumGroupListener::POST_EVENT);
    }
};

$demo->setEventManager($em);
$demo->run();
