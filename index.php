<?php

require_once 'vendor/autoload.php';
require_once 'PackageScan.php';
require_once 'User.php';

use FastFeed\Factory;
use Aura\Sql\ExtendedPdo;

$pdo = new ExtendedPdo(
	'mysql:host='.$_SERVER['DB_HOST'].';dbname='.$_SERVER['DB_NAME'],
	$_SERVER['DB_USER'],
	$_SERVER['DB_PASS'],
	array(), array()
);
$packageScan = new PackageScan($pdo);
$user = new User($pdo);

// Application ------------
$app = new \Slim\Slim(array(
	'debug' => false
));

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
		$queued = array();

		foreach ($contents->packages as $package) {
			// see if we have any for that package, if not queue it up
			if ($packageScan->packageExists($package->name) == false) {
				$queued[] = $package->name;
				$packageScan->addToQueue($package);
			}

			$data = array(
				'name' => $package->name,
				'feedId' => $feedId
			);
			$packageScan->insertFeedPackage($data);
		}
	}
	$hash = $packageScan->getHashByFeedId($feedId);

	// Be sure we remove our tmp file
	if (is_file($_FILES['composerlock']['tmp_name'])) {
		unlink($_FILES['composerlock']['tmp_name']);
	}

	$app->render('upload.php', array(
		'hash' => $hash,
		'queued' => $queued
	));
});

$app->get('/queue/:hash', function($hash) use ($app, $packageScan) {
	$app->render('queue.php', array(
		'items' => $packageScan->getFeedQueued($hash)
	));
});

// Usage routes ----------
$app->get('/usage', function() use ($app, $packageScan) {
	$app->render(
		'usage.php',
		array('packages' => $packageScan->getPopularPackages())
	);
});
$app->post('/usage', function() use ($app, $packageScan) {
	$packageName = $app->request->post('name');
	$count = $packageScan->getPackageUsageCount($packageName);

	echo json_encode(array(
		'name' => $packageName,
		'count' => $count['cid']
	));
});

// User routes ---------
$app->group('/user', function() use ($app, $user) {
	$app->get('/login', function() use ($app, $user) {
		$app->render('user/login.php');
	});
	$app->get('/register', function() use ($app, $user) {
		$app->render('user/register.php');
	});
	$app->post('/register', function() use ($app, $user) {
		$data = $app->request->post();
		$errors = array();

		if (empty($data['username'])) {
			$errors['username'] = true;
		}
		if (empty($data['password'])) {
			$errors['password'] = true;
		}
		if (empty($data['email'])) {
			$errors['email'] = true;
		}
		if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) !== $data['email']) {
			$errors['email'] = true;
		}

		if (empty($errors)) {
			// add the user
			$user->add($data);
		}

		$app->render(
			'user/register.php',
			array('errors' => $errors, 'data' => $data)
		);
	});
	$app->get('/view', function() use ($app, $user) {
		$app->render('user/view.php');
	});
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

$app->error(function(\Exception $e) use ($app) {
	$app->log->error($e->getMessage());
	return $app->render('error.php');
});
$app->run();
