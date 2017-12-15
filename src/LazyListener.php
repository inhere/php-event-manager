<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/15 0015
 * Time: 00:07
 */

namespace Inhere\Event;

/**
 * Class LazyListener - 将callable包装成对象
 * @package Inhere\Event
 */
class LazyListener implements EventHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        return ($this->callback)($event);
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
