<?php

declare(strict_types=1);

require_once("vendor/autoload.php");

$log = getLogger();

$dayFolderInt = (int) $argv[1];
$mainFile = sprintf('src/day_%s/main.php', $dayFolderInt);

if (!file_exists($mainFile)) {
    $log->error(sprintf("Could not find main file for day %s. You need to create a file at src/day_%s/main.php", $dayFolderInt, $dayFolderInt));
    exit(0);
}

$log->info(sprintf('~~~ Day %s ~~~', $dayFolderInt));
$log->info(sprintf('running file: %s', $mainFile));

try {
    require($mainFile);
} catch (Exception $e) {
    $log->error($e->getMessage());
}

$log->info(sprintf('~~~ End Day %s ~~~', $dayFolderInt));

exit(0);
