<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-07
 * Time: 14:12
 */

namespace Inhere\Event\Examples;

use Inhere\Event\Event;
use Inhere\Event\EventManagerAwareTrait;
use Inhere\Event\EventManager;

/**
 * Class App
 * @package Inhere\Event\Examples
 */
class App
{
    const ON_START = 'app.start';
    const ON_STOP = 'app.stop';
    const ON_BEFORE_REQUEST = 'app.beforeRequest';
    const ON_AFTER_REQUEST = 'app.afterRequest';

    use EventManagerAwareTrait;

    public function __construct(EventManager $em)
    {
        $this->setEventManager($em);

        $this->eventManager->trigger(self::ON_START, new Event('start', [
            'key' => 'val'
        ]));
    }

    public function run()
    {
        $this->eventManager->trigger(self::ON_BEFORE_REQUEST, new Event('beforeRequest'));

        $sleep = 0;

        echo 'handling ';

        while ($sleep <= 3) {
            $sleep++;
            echo '.';
            sleep(1);
        }

        echo "\n";

        $this->eventManager->trigger(self::ON_AFTER_REQUEST, new Event('afterRequest'));
    }

    public function __destruct()
    {
        $this->eventManager->trigger(self::ON_STOP, new Event('stop', [
            'key1' => 'val1'
        ]));
    }
}
