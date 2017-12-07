<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-16
 * Time: 17:50
 */

namespace Inhere\Event\Examples;

use Inhere\Event\EventInterface;
use Inhere\Event\HandlerInterface;

/**
 * Class SingleListener
 * @package Inhere\Event
 */
class EventHandler implements HandlerInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        // TODO: Implement handle() method.
        return true;
    }
}
