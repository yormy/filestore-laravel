<?php

declare(strict_types=1);

if (! isset($argv[1])) {
    echo 'You must specify the clover.xml file'.PHP_EOL;
    exit;
}

if (! isset($argv[2])) {
    echo 'You must specify a minimal accepted value'.PHP_EOL;
    exit;
}

$inputFile = $argv[1];
$acceptedPercentage = (int) $argv[2];
$percentage = min(100, max(0, $acceptedPercentage));

if (! file_exists($inputFile)) {
    throw new InvalidArgumentException('Invalid input file provided');
}

if (! $percentage) {
    throw new InvalidArgumentException('An integer checked percentage must be given as second parameter');
}

$xml = new SimpleXMLElement(file_get_contents($inputFile));
$metrics = $xml->xpath('//metrics');
$totalElements = 0;
$checkedElements = 0;

foreach ($metrics as $metric) {
    $totalElements += (int) $metric['elements'];
    $checkedElements += (int) $metric['coveredelements'];
}

$coverage = $checkedElements / $totalElements * 100;

if ($coverage < $percentage) {
    echo "\033[31mCode coverage is ".round($coverage, 2).'%, which is below the accepted '.$percentage."% \033[39m".PHP_EOL;
    exit(1);
}

echo "\033[32mCode coverage is ".round($coverage, 2)."% - OK!\033[39m".PHP_EOL;
