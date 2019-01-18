<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-16
 * Time: 17:50
 */

namespace Inhere\Event\Examples;

use Inhere\Event\EventHandlerInterface;
use Inhere\Event\EventInterface;

/**
 * Class SingleListener
 * @package Inhere\Event
 */
class ExamHandler implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        $pos = __METHOD__;
        echo "handle the event '{$event->getName()}' on the: $pos\n";

        return true;
    }
}
