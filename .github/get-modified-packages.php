<?php

declare(strict_types=1);

/*
 * Given a list of all packages, find the package that have been modified.
 */

if (3 > $_SERVER['argc']) {
    echo 'Usage: ' . $_SERVER['argv'][0] . '\n';
    exit(1);
}

$allPackages = json_decode($_SERVER['argv'][1], true, 512, \JSON_THROW_ON_ERROR);
$modifiedFiles = json_decode($_SERVER['argv'][2], true, 512, \JSON_THROW_ON_ERROR);

// Sort to get the longest name first (match bridge not component)
usort(
    $allPackages,
    function($a, $b) {
        return strlen($b) <=> strlen($a) ?: $a <=> $b;
    }
);

$newPackage = [];
$modifiedPackages = [];
foreach ($modifiedFiles as $file) {
    foreach ($allPackages as $package) {
        if (str_starts_with($file, $package)) {
            $modifiedPackages[$package] = true;
            if (str_ends_with($file, 'LICENSE')) {
                /*
                 * There is never a reason to modify the LICENSE file, this diff
                 * must be adding a new package
                 */
                $newPackage[$package] = true;
            }
            break;
        }
    }
}

$output = [];
foreach ($modifiedPackages as $directory => $bool) {
    $composerData = json_decode(file_get_contents($directory . '/composer.json'), true);
    $name = $composerData['name'] ?? 'unknown';
    $output[] = [
        'name' => $name,
        'directory' => $directory,
        'new' => $newPackage[$directory] ?? false,
    ];
}

echo json_encode($output);
