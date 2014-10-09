<?php

require_once 'vendor/autoload.php';
require_once 'PackageScan.php';

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
$queueItems = $packageScan->getFromQueue();

foreach ($queueItems as $item) {
    // be sure it's HTTP/HTTPS
    $url = parse_url($item['package_url']);

    // If it's internal or not http/https, we can't really get it
    if ($url['scheme'] !== 'http' && $url['scheme'] !== 'https') {
        $packageScan->removeFromQueue($name);
        continue;
    }


    $url = str_replace('.git', '/releases.atom', $item['package_url']);
    $feed = Feed::loadAtom($url);
    $count = 0;

    foreach ($feed->entry as $entry) {
        if ($count > 0) { continue; }
        $version = str_replace('v', '', $entry->title);
        $name = $item['package_name'];

        list($version, $majorVersion, $minorVersion, $patchVersion)
            = $packageScan->parseVersion($version);

        $date = new \DateTime('@'.$entry->timestamp);
        $data = array(
            'name' => $name,
            'version' => $version,
            'major_version' => $majorVersion,
            'minor_version' => $minorVersion,
            'patch_version' => $patchVersion,
            'date_posted' => $date->format('Y-m-d H:i:s'),
            'description' => $item['package_description'],
            'source' => $item['package_source'],
            'author' => $item['package_author']
        );
        $count = 1;

        if ($packageScan->insertRelease($data, $name, $version)) {
            echo 'Adding '.$name." (".$version.")\n";
            $packageScan->removeFromQueue($name);
        }
    }
}
