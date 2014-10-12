<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../PackageScan.php';

// use FastFeed\Factory;
use Aura\Sql\ExtendedPdo;

$config = parse_ini_file('/etc/packagetrack.ini', true);


$pdo = new ExtendedPdo(
    'mysql:host='.$config['database']['DB_HOST'].';dbname='.$config['database']['DB_NAME'],
    $config['database']['DB_USER'],
    $config['database']['DB_PASS'],
    array(), array()
);
$packageScan = new PackageScan($pdo);

/**
 * Fetch the package's composer.json, resolve dependencies
 * and add the package releases to the database
 */

function buildUrl($packageName)
{
    $url = 'https://github.com/'.$packageName.'/releases.atom';
    return $url;
}

$package = $_SERVER['argv'][1];
$url = 'https://raw.githubusercontent.com/'.$package.'/master/composer.json';

echo "Fetching package: ".$package." (".$url.")\n";

// https://raw.githubusercontent.com/psecio/jwt/master/composer.json
$composerData = json_decode(file_get_contents($url));
$packageCount = 0;

if ($composerData == false) {
    echo "Error fetching package: ".$package."\n\n";
}

if (isset($composerData->require)) {
    foreach ($composerData->require as $packageName => $version) {
        if ($packageName == 'php') {
            continue;
        }
        echo 'Fetching for: '.$packageName."\n";
        $packageReleases = Feed::loadAtom(buildUrl($packageName));
        $count = 0;

        foreach ($packageReleases->entry as $entry) {
            if ($count > 0) { continue; }

            // Get the general data for the package
            $url = 'https://raw.githubusercontent.com/'.$packageName.'/master/composer.json';
            $packageData = json_decode(file_get_contents($url));
            $version = str_replace('v', '', $entry->title);

            list($version, $majorVersion, $minorVersion, $patchVersion)
                = $packageScan->parseVersion($version);

            $date = new \DateTime('@'.$entry->timestamp);
            $data = array(
                'name' => $packageName,
                'version' => $version,
                'major_version' => $majorVersion,
                'minor_version' => $minorVersion,
                'patch_version' => $patchVersion,
                'date_posted' => $date->format('Y-m-d H:i:s'),
                'description' => $packageData->description,
                'source' => 'https://github.com/'.$packageData->name,
                'author' => $packageData->authors[0]->name
            );

            if ($packageScan->insertRelease($data, $packageName, $version)) {
                $packageCount++;
                echo 'Adding '.$packageName." (".$version.")\n";
            }

            $count++;
        }
    }
}
echo "\nFinished, ".$packageCount." package(s) inserted";
echo "\n\n";

?>