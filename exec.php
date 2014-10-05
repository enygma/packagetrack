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
	$version = str_replace('v', '', $matches[2]);

	$parts = explode('.', $version);
	$majorVersion = $parts[0];
	$minorVersion = $parts[1];
	$patchVersion = (isset($parts[2]) == true) ? $parts[2] : '';

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

	$columns = array();
	$bind = array();
	foreach ($data as $column => $d) {
		$columns[] = $column;
		$bind[] = ':'.$column;
	}
	// Be sure it doesn't already exist
	$sql = 'select id from releases where name = :name and version = :version';
	$result = $pdo->fetchAll($sql, array('name' => $name, 'version' => $version));
	if (count($result) == 0) {
		echo 'Adding '.$name." (".$version.")\n";
		$sql = 'insert into releases ('.implode(',', $columns).', date_added) values ('.implode(',', $bind).', NOW())';
		$result = $pdo->perform($sql, $data);
	}
}
