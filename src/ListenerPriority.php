<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/8/27
 * Time: 下午12:37
 */

namespace Inhere\Event;

/**
 * Class ListenerPriority - 监听器优先级级别 部分常量
 * @package Inhere\Event
 */
final class ListenerPriority
{
    public const MIN = -300;
    public const LOW = -200;
    public const BELOW_NORMAL = -100;
    public const NORMAL = 0;
    public const ABOVE_NORMAL = 100;
    public const HIGH = 200;
    public const MAX = 300;
}
