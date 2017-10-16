<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-16
 * Time: 16:47
 */

namespace Inhere\Event;

/**
 * Class EventManager
 * @package Inhere\Event
 */
class EventManager implements EventManagerInterface
{
    /**
     * 1.事件存储
     * @var EventInterface[]
     * [
     *     'event name' => (object)EventInterface -- event description
     * ]
     */
    protected $events = [];

    /**
     * 2.监听器存储
     * @var ListenerQueue[] array
     */
    protected $listeners = [];

    public function __destruct()
    {
        $this->clear();
    }

    public function clear()
    {
        $this->events = $this->listeners = [];
    }

    /*******************************************************************************
     * Event manager
     ******************************************************************************/

    /**
     * 添加一个不存在的事件
     * @param Event|string $event | event name
     * @param array $params
     * @return $this
     */
    public function addEvent($event, array $params = [])
    {
        if (is_string($event)) {
            $event = new Event(trim($event), $params);
        }

        /** @var $event Event */
        if (($event instanceof EventInterface) && !isset($this->events[$event->getName()])) {
            $this->events[$event->getName()] = $event;
        }

        return $this;
    }

    /**
     * 设定一个事件处理
     * @param string|EventInterface $event
     * @param array $params
     * @return $this
     */
    public function setEvent($event, array $params = [])
    {
        if (is_string($event)) {
            $event = new Event(trim($event), $params);
        }

        if ($event instanceof EventInterface) {
            $this->events[$event->getName()] = $event;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getEvent($name, $default = null)
    {
        return $this->events[$name] ?? $default;
    }


    public function removeEvent($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->events[$event])) {
            unset($this->events[$event]);
        }

        return $this;
    }

    /**
     * @param $event
     * @return bool
     */
    public function hasEvent($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->events[$event]);
    }


    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param $events
     */
    public function setEvents(array $events)
    {
        foreach ($events as $key => $event) {
            $this->setEvent($event);
        }
    }

    /**
     * @return int
     */
    public function countEvents()
    {
        return count($this->events);
    }

    /*******************************************************************************
     * Listener manager
     ******************************************************************************/

    /**
     * Attaches a listener to an event
     * @param string $event the event to attach too
     * @param callable|ListenerInterface|mixed $callback a callable listener function
     * @param int $priority the priority at which the $callback executed
     * @return bool true on success false on failure
     */
    public function attach($event, $callback, $priority = 0)
    {
        $this->addListener($callback, [$event => $priority]);

        return true;
    }

    /**
     * Detaches a listener from an event
     * @param string $event the event to attach too
     * @param callable $callback a callable function
     * @return bool true on success false on failure
     */
    public function detach($event, $callback)
    {
        return $this->removeListener($callback, $event);
    }

    /**
     * 添加监听器 并关联到 某一个(多个)事件
     * @param \Closure|callback $listener 监听器
     * @param array|string|int $definition 事件名，优先级设置
     * @return $this
     * @throws \InvalidArgumentException
     * @example
     *     $definition = [
     *        'event name' => int level
     *     ]
     * OR
     *     $definition = 'event name'
     * OR
     *     $definition = 1 // The priority of the listener 监听器的优先级
     */
    public function addListener($listener, $definition = null)
    {
        if (!is_object($listener)) {
            throw new \InvalidArgumentException('The given listener must is an object or a Closure.');
        }

        $priority = ListenerPriority::NORMAL;

        if (is_numeric($definition)) {
            $priority = (int)$definition;
            $definition = null;
        } elseif (is_string($definition)) { // 仅是个 事件名称
            $definition = [$definition => $priority];
        } elseif ($definition instanceof EventInterface) { // 仅是个 事件对象,取出名称
            $definition = [$definition->getName() => $priority];
        }

        // 1. is a Closure or callback(String|Array)
        if (is_callable($listener)) {
            // 设置要将监听器关联到什么事件?
            if (!$definition) {
                throw new \InvalidArgumentException('Please set the listener to events associated with?');
            }

            // 循环: 将 监听器 关联到 各个事件
            foreach ($definition as $eventName => $level) {
                $eventName = trim($eventName);

                if (!isset($this->listeners[$eventName])) {
                    $this->listeners[$eventName] = new ListenerQueue;
                }

                $this->listeners[$eventName]->add($listener, (int)$level);
            }

            return $this;
        }

        // 2. is a Object.

        // 得到要绑定的监听器中所有方法名
        $methods = get_class_methods($listener);
        $eventNames = [];

        // 取出所有方法列表中 需要关联的事件(方法)名
        if ($definition) {
            $eventNames = array_intersect($methods, array_keys($definition));
        }

        // 循环: 将 监听器 关联到 各个事件
        foreach ($eventNames as $name) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = new ListenerQueue;
            }

            $level = $definition[$name] ?? $priority;

            $this->listeners[$name]->add($listener, (int)$level);
        }

        return $this;
    }

    /**
     * 是否存在 对事件的 监听队列
     * @param  EventInterface|string $event
     * @return boolean
     */
    public function hasListenerQueue($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->listeners[$event]);
    }

    /**
     * @see self::hasListenerQueue() alias method
     * @param  EventInterface|string $event
     * @return boolean
     */
    public function hasListeners($event)
    {
        return $this->hasListenerQueue($event);
    }

    /**
     * 是否存在(对事件的)监听器
     * @param $listener
     * @param  EventInterface|string $event
     * @return bool
     */
    public function hasListener($listener, $event = null)
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            if (isset($this->listeners[$event])) {
                return $this->listeners[$event]->has($listener);
            }
        } else {
            foreach ($this->listeners as $queue) {
                if ($queue->has($listener)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 获取事件的一个监听器的优先级别
     * @param $listener
     * @param  string|EventInterface $event
     * @return int|null
     */
    public function getListenerLevel($listener, $event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event]->getLevel($listener);
        }

        return null;
    }

    /**
     * 获取事件的所有监听器
     * @param  string|EventInterface $event
     * @return array ListenersQueue[]
     */
    public function getListeners($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event]->getAll();
        }

        return [];
    }

    /**
     * 统计获取事件的监听器数量
     * @param  string|EventInterface $event
     * @return int
     */
    public function countListeners($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->listeners[$event]) ? count($this->listeners[$event]) : 0;
    }

    /**
     * 移除对某个事件的监听
     * @param $listener
     * @param null|string|EventInterface $event
     * 为空时，移除监听者队列中所有名为 $listener 的监听者
     * 否则， 则移除对事件 $event 的监听者
     * @return bool
     */
    public function removeListener($listener, $event = null)
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // 存在对这个事件的监听队列
            if (isset($this->listeners[$event])) {
                $this->listeners[$event]->remove($listener);
            }
        } else {
            foreach ($this->listeners as $queue) {
                /**  @var $queue ListenerQueue */
                $queue->remove($listener);
            }
        }

        return true;
    }

    /**
     * Clear all listeners for a given event
     * @param  string|EventInterface $event
     * @return void
     */
    public function clearListeners($event)
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // 存在对这个事件的监听队列
            if (isset($this->listeners[$event])) {
                unset($this->listeners[$event]);
            }
        } else {
            $this->listeners = [];
        }
    }

    /**
     * Trigger an event
     * Can accept an EventInterface or will create one if not passed
     * @param  string|EventInterface $event
     * @param  mixed|string $target
     * @param  array|mixed $argv
     * @return mixed
     */
    public function trigger($event, $target = null, $argv = array())
    {
        if (!($event instanceof EventInterface)) {
            if (isset($this->events[$event])) {
                $event = $this->events[$event];
            } else {
                $event = new Event($event);
            }
        }

        /** @var EventInterface $event */
        $name = $event->getName();
        $params = array_merge($event->getParams(), $argv);
        $event->setParams($params);
        $event->setTarget($target);

        if (isset($this->listeners[$name])) {
            // 循环调用监听器，处理事件
            foreach ($this->listeners[$name] as $listener) {
                if ($event->isPropagationStopped()) {
                    break;
                }

                /** @var callable|\Closure|Callback $listener */
                if ($listener instanceof \StdClass) {
                    $cb = $listener->callback;
                    $cb($event);
                } elseif (is_callable($listener)) {
                    $listener($event);
                } else {
                    $listener->$name($event);
                }
            }
        }

        return $event;
    }
}
