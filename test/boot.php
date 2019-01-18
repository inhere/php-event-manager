<?php
/**
 * phpunit
 * // set boot
 * phpunit --bootstrap tests/boot.php tests
 * // output coverage without xdebug
 * phpdbg -dauto_globals_jit=Off -qrr /usr/local/bin/phpunit --coverage-text
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    $file = null;

    if (0 === strpos($class, 'Inhere\Event\Examples\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\Event\Examples\\')));
        $file = dirname(__DIR__) . "/examples/{$path}.php";
    } elseif (0 === strpos($class, 'Inhere\EventTest\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\EventTest\\')));
        $file = dirname(__DIR__) . "/{$path}.php";
    } elseif (0 === strpos($class, 'Inhere\Event\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\Event\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});
