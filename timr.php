<?php

require_once 'vendor/autoload.php';

use Kopernikus\TimrReportManager\Timr;

$rootDir = __DIR__;

(new Timr($rootDir))->run();


