<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 9:55
 */

namespace Inhere\Event;

/**
 * Class EventSubscriberInterface - 自定义配置多个相关的事件的监听器
 * @package Inhere\Event
 */
interface EventSubscriberInterface
{
    /**
     * 配置事件与对应的处理方法(可以配置优先级)
     * @return array
     */
    public static function getSubscribedEvents(): array;
    // {
    //     return [
    //         // event name => handler, can with priority
    //         KernelEvents::CONTROLLER => ['onKernelController', ListenerPriority::LOW],
    //         KernelEvents::VIEW => 'onKernelView',
    //     ];
    // }
}
