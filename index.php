<?php

namespace TurpEdit;

// Ensure vendor libraries exist
$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    die("Please run: <i>bin/ install</i>");
}

use TurpEdit\Common\TurpEdit;

$loader = require_once $autoloader;

echo TE_VERSION;
echo TurpEdit::testFunction();