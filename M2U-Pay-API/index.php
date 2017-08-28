<?php
require __DIR__ . '/includes/autoload.php';

$route = explode('/', get_route());

if (substr($route[0], 0, 6) === 'create') {
    $file = __DIR__ . '/controller/create.php';
} else if ($route[0] === 'bill') {
    $file = __DIR__ . '/controller/bill.php';
} else if (substr($route[0], 0, 8) === 'callback') {
    $file = __DIR__ . '/controller/ajax.php';
} else {
    $file = __DIR__ . '/controller/index.php';
}

require $file;
