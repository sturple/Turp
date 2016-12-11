<?php

namespace TurpEdit;

// Ensure vendor libraries exist
$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    die("Please run: <i>bin/ install</i>");
}

use TurpEdit\Common\TurpEdit;


$loader = require_once $autoload;

$tedit = TurpEdit::instance(
    array(
        'loader' => $loader
    )
);

// Process the page
try {
    print_R( $tedit->process() );
} catch (\Exception $e) {
   // $tedit->fireEvent('onFatalException', new Event(['exception' => $e]));
    throw $e;
}