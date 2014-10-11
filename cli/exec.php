<?php

require_once 'vendor/autoload.php';
require_once 'PackageScan.php';

use FastFeed\Factory;
use Aura\Sql\ExtendedPdo;

$config = parse_ini_file('/etc/packagetrack.ini', true);


$pdo = new ExtendedPdo(
	'mysql:host='.$config['database']['DB_HOST'].';dbname='.$config['database']['DB_NAME'],
	$config['database']['DB_USER'],
	$config['database']['DB_PASS'],
	array(), array()
);
$packageScan = new PackageScan($pdo);

$fastFeed = Factory::create();
$fastFeed->addFeed('packagist-releases', 'https://packagist.org/feeds/releases.rss');
$items = $fastFeed->fetch('packagist-releases');

foreach ($items as $item) {
	preg_match('/(.*?) \((.+)\)/', $item->getName(), $matches);
	$name = $matches[1];

	list($version, $majorVersion, $minorVersion, $patchVersion)
		= $packageScan->parseVersion($matches[2]);

	$data = array(
		'name' => $name,
		'version' => $version,
		'major_version' => $majorVersion,
		'minor_version' => $minorVersion,
		'patch_version' => $patchVersion,
		'date_posted' => $item->getDate()->format('Y-m-d H:i:s'),
		'description' => $item->getContent(),
		'source' => $item->getSource(),
		'author' => $item->getAuthor()
	);

	if ($packageScan->insertRelease($data, $name, $version)) {
		echo 'Adding '.$name." (".$version.")\n";
	}
}
