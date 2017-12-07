<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午10:51
 */

namespace Inhere\Event;

/**
 * Interface ListenerInterface
 * @package Inhere\Event
 */
interface HandlerInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event);
}
