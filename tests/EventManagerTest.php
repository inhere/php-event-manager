<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-11-27
 * Time: 9:47
 */

namespace Inhere\Event\Tests;

use Inhere\Event\EventManager;
use Inhere\Event\EventManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class EventManagerTest
 */
class EventManagerTest extends TestCase
{
    public function testCreate()
    {
        $em = new EventManager();

        $this->assertInstanceOf(EventManagerInterface::class, $em);
    }
}