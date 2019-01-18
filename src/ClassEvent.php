<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-27
 * Time: 9:30
 */

namespace Inhere\Event;

/**
 * Class ClassEvent - the Class Level Event
 *
 * @reference yii2 Event
 * @package Inhere\Event
 */
class ClassEvent
{
    /**
     * registered Events
     * @var array
     * [
     *   'event' => bool, // is once event
     * ]
     */
    private static $events = [];

    /**
     * register a event handler
     * @param string|mixed $class
     * @param mixed        $event
     * @param callable     $handler
     */
    public static function on(string $class, string $event, callable $handler)
    {
        $class = \ltrim($class, '\\');

        if (self::isSupportedEvent($event)) {
            self::$events[$event][$class] = $handler;
        }
    }

    /**
     * trigger event
     * @param string $event
     * @param array  $args
     * @return bool
     */
    public static function fire(string $event, array $args = [])
    {
        if (!isset(self::$events[$event])) {
            return false;
        }

        // call event handlers of the event.
        foreach ((array)self::$events[$event] as $cb) {
            // return FALSE to stop go on handle.
            if (false === $cb(...$args)) {
                break;
            }
        }

        // is a once event, remove it
        if (self::$events[$event]) {
            return self::removeEvent($event);
        }

        return true;
    }

    /**
     * remove event and it's handlers
     * @param $event
     * @return bool
     */
    public static function off(string $event)
    {
        return self::removeEvent($event);
    }

    /**
     * @param $event
     * @return bool
     */
    public static function removeEvent(string $event): bool
    {
        if (self::hasEvent($event)) {
            unset(self::$events[$event]);
            return true;
        }

        return false;
    }

    /**
     * @param $event
     * @return bool
     */
    public static function hasEvent($event)
    {
        return isset(self::$events[$event]);
    }

    /**
     * @param $event
     * @return bool
     */
    public static function isOnce(string $event): bool
    {
        if (self::hasEvent($event)) {
            return self::$events[$event];
        }

        return false;
    }

    /**
     * check $name is a supported event name
     * @param $event
     * @return bool
     */
    public static function isSupportedEvent(string $event): bool
    {
        return $event && \preg_match('/^[a-zA-z][\w-]+$/', $event);
    }

    /**
     * @return array
     */
    public static function getEvents(): array
    {
        return self::$events;
    }

    /**
     * @return int
     */
    public static function countEvents(): int
    {
        return \count(self::$events);
    }
}
