<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-16
 * Time: 17:50
 */

namespace Inhere\Event;

/**
 * Class SingleListener
 * @package Inhere\Event
 */
class SingleListener implements SingleListenerInterface
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
