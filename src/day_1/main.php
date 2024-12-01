<?php

use App\Lib\Benchmark;

require_once 'vendor/autoload.php';


$log = getLogger();
$bench = new Benchmark($log);

/**
 * Plan of attack
 * sort both lists
 * run through the pairs and add update the distance
*/

$log->info("parse input.txt");

$bench->start('load_input');
$content = file_get_contents('src/day_1/input.txt');

$bench->end('load_input');
