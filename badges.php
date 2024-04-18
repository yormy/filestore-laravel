<?php

declare(strict_types=1);

$scores = [];
$scores['stan']['code'] = ['low' => 30, 'medium' => 60];
$scores['coverage'] = ['low' => 30, 'medium' => 60];

badgeTestCoverage();
badgeInsightsSecurity();
badgeInsightsCode();
badgeStan();

function badgeTestCoverage(): void
{
    global $scores;
    $image = 'test_coverage.svg';
    $label = 'Coverage';

    $path = 'coverage/clover.xml';

    if (! file_exists($path)) {
        $coverage = 'missing';
        createBadge($label, $coverage, 'red', $image);

        return;
    }

    $xml = new SimpleXMLElement(file_get_contents($path));
    $metrics = $xml->xpath('//metrics');
    $totalElements = 0;
    $checkedElements = 0;

    foreach ($metrics as $metric) {
        $totalElements += (int) $metric['elements'];
        $checkedElements += (int) $metric['coveredelements'];
    }

    $coverage = (int) ($checkedElements / $totalElements * 100);

    if ($coverage < $scores['coverage']['low']) {
        $color = 'red';
    } elseif ($coverage < $scores['coverage']['medium']) {
        $color = 'yellow';
    } else {
        $color = 'green';
    }

    $coverage = "$coverage";
    createBadge($label, $coverage, $color, $image, true);
}

function badgeInsightsSecurity(): void
{
    $image = 'insights_security.svg';
    $label = 'Security';

    $path = './badges/phpinsights.json';
    if (! file_exists($path)) {
        createBadge($label, 'missing', 'red', $image);

        return;
    }

    $insights = getInsights();
    if (! array_key_exists('security_issues', $insights['summary'])) {
        $color = 'red';
        $issues = 'unknown';
    } else {
        $issues = (int) $insights['summary']['security_issues'];
        if ($issues === 0) {
            $color = 'green';
        } else {
            $color = 'red';
        }

        $issues = "$issues";
    }

    createBadge($label, $issues, $color, $image);
}

function badgeInsightsCode(): void
{
    global $scores;
    $image = 'insights_code.svg';
    $label = 'Code';

    $path = './badges/phpinsights.json';
    if (! file_exists($path)) {
        createBadge($label, 'missing', 'red', $image);

        return;
    }

    $insights = getInsights();

    $codePercentage = (int) $insights['summary']['code'];
    if ($codePercentage < $scores['stan']['code']['low']) {
        $color = 'red';
    } elseif ($codePercentage < $scores['stan']['code']['medium']) {
        $color = 'yellow';
    } else {
        $color = 'green';
    }

    $codePercentage = "$codePercentage";
    createBadge($label, $codePercentage, $color, $image, true);
}

function badgeStan(): void
{
    $path = './phpstan-baseline.neon';
    $label = 'phpstan';
    $image = 'stan.svg';

    if (file_exists($path)) {
        $value = 'baselined';
        $color = 'red';
    } else {
        $value = 'active';
        $color = 'green';
    }

    createBadge($label, $value, $color, $image);
}

function createBadge(string $label, string $value, string $color, string $filename, bool $percentage = false): void
{
    $label = str_replace(' ', '_', $label);

    $unit = '';
    if ($percentage) {
        $unit = '%25';
    }

    exec("wget https://img.shields.io/badge/{$label}-{$value}{$unit}-{$color} -O badges/{$filename}");
}

function getInsights(): array
{
    $path = './badges/phpinsights.json';
    $jsonString = file_get_contents($path);

    return json_decode($jsonString, true);
}
