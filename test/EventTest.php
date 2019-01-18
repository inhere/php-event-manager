<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-01-18
 * Time: 23:56
 */

namespace Inhere\EventTest;

use Inhere\Event\Event;
use PHPUnit\Framework\TestCase;

/**
 * Class EventTest
 * @package Inhere\EventTest
 */
class EventTest extends TestCase
{
    public function testEvent()
    {
        $e = new Event('test');
    }
}
