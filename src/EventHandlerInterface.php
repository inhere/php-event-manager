<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午10:51
 */

namespace Inhere\Event;

/**
 * Interface EventHandlerInterface - 单个事件的监听处理器
 * @package Inhere\Event
 */
interface EventHandlerInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event);
}
