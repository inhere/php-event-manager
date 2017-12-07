<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-07
 * Time: 11:22
 */

namespace Inhere\Event;

/**
 * Trait EventAwareTrait
 * @package Inhere\Event
 */
trait EventAwareTrait
{
    /**
     * @var EventManager|EventManagerInterface
     */
    protected $eventManager;

    /**
     * @return EventManager|EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @param EventManager|EventManagerInterface $eventManager
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}