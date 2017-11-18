<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-16
 * Time: 17:50
 */

namespace Inhere\Event;

/**
 * Class Listener
 * @package Inhere\Event
 */
class Listener implements ListenerInterface
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
