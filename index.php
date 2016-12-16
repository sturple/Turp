<?php

namespace TurpEdit;

// Ensure vendor libraries exist
$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    die("Please run: <i>bin/ install</i>");
}

use TurpEdit\Common\TurpEdit;


$loader = require_once $autoload;

$tedit = Turp::instance(
    array(
        'loader' => $loader
    )
);

// Process the page
try {
     $tedit->process() ;
} catch (\Exception $e) {
    //$tedit['dispatcher']->dispatch('error.fatalException', new Event(['excpetion'=>$e]));
   // $tedit->fireEvent('onFatalException', new Event(['exception' => $e]));
    throw $e;
}