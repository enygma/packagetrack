<?php

require_once 'vendor/autoload.php';
require_once 'PackageScan.php';

use FastFeed\Factory;
use Aura\Sql\ExtendedPdo;

$pdo = new ExtendedPdo(
	'mysql:host='.$_SERVER['DB_HOST'].';dbname='.$_SERVER['DB_NAME'],
	$_SERVER['DB_USER'],
	$_SERVER['DB_PASS'],
	array(), array()
);
$packageScan = new PackageScan($pdo);

// Application ------------
$app = new \Slim\Slim();

$app->get('/', function() use ($app) {
	$app->render('index.php');
});
$app->post('/upload', function() use ($app, $packageScan, $pdo) {
	$lockContent = file_get_contents($_FILES['composerlock']['tmp_name']);

	// Try to parse it
	$contents = @json_decode($lockContent);
	if ($contents !== false) {
		$hash = hash('sha1', $lockContent.'|'.time());
		$feedId = $packageScan->insertFeed($hash);

		foreach ($contents->packages as $package) {
			// see if we have any for that package
			if ($packageScan->packageExists($package->name) == false) {
				$packageScan->getPackageRelease($package->name);
			}

			$data = array(
				'name' => $package->name,
				'feedId' => $feedId
			);
			$packageScan->insertFeedPackage($data);
		}
	}
	$hash = $packageScan->getHashByFeedId($feedId);
	$app->render('upload.php', array('hash' => $hash));
});

// Feed route ----------
$app->get('/feed/:hash', function($hash) use ($app, $packageScan, $pdo) {

	$packageScan->updateLastViewed($hash);
	$results = $packageScan->getFeedPackages($hash);

	$packages = array();
	foreach ($results as $result) {
		$packages[] = strtolower($result['name']);
	}

	$app->response->headers->set('Content-Type', 'text/xml');
	$app->render('feed.php', array(
		'items' => $packageScan->getReleaseByPackage($packages),
		'hash' => $hash
	));
});

$app->run();