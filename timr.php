<?php

require_once 'vendor/autoload.php';

use Kopernikus\TimrReportManager\Timr;

(new Timr(__DIR__))->run();