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

$libDir = dirname(__DIR__);
$npMap  = [
    'Inhere\\Event\\Examples\\' => $libDir . '/examples/',
    'Inhere\\Event\\'           => $libDir . '/src/',
    'Inhere\\EventTest\\'       => $libDir . '/test/',
];

spl_autoload_register(function ($class) use ($npMap) {
    foreach ($npMap as $np => $dir) {
        $file = $dir . str_replace('\\', '/', substr($class, strlen($np))) . '.php';

        if (file_exists($file)) {
            include $file;
        }
    }
});
