<?php

class PackageScan
{
	private $pdo;

	public function __construct($pdo)
	{
		$this->setPdo($pdo);
	}

	public function setPdo($pdo)
	{
		$this->pdo = $pdo;
	}
	public function getPdo()
	{
		return $this->pdo;
	}

	public function insertFeed($hash)
	{
		$pdo = $this->getPdo();
		$sql = 'insert into feed (hash, date_added) values (:hash, NOW())';
		$result = $this->getPdo()->perform($sql, array('hash' => $hash));
		return $pdo->lastInsertId();
	}

	public function insertFeedPackage($data)
	{
		$sql = 'insert into feed_packages (name, feed_id, date_added) values (:name, :feedId, NOW())';
		$result = $this->getPdo()->perform($sql, $data);
		return $result;
	}

	public function getHashByFeedId($feedId)
	{
		$sql = 'select hash from feed where id = :id';
		$result = $this->getPdo()->fetchAll($sql, array('id' => $feedId));
		return $result[0]['hash'];
	}

	public function updateLastViewed($hash)
	{
		$sql = 'update feed set last_view = NOW() where hash = :hash';
		return $this->getPdo()->perform($sql, array('hash' => $hash));
	}

	public function getFeedPackages($hash)
	{
		$sql = 'select f.id, fp.name from feed f, feed_packages fp where f.hash = :hash'
		.' and f.id = fp.feed_id order by f.date_added desc';

		$results = $this->getPdo()->fetchAll($sql, array('hash' => $hash));
		return $results;
	}

	public function getReleaseByPackage(array $packages)
	{
		$pdo = $this->getPdo();
		$sql = 'select * from releases where lower(name) in ('.$pdo->quote($packages).') order by date_posted desc';
		$results = $pdo->fetchAll($sql);
		return $results;
	}

	public function packageExists($packageName)
	{
		$sql = 'select count(id) as ct from releases where lower(name) = :name';
		$results = $this->getPdo()->fetchAll($sql, array('name' => $packageName));
		return ($results[0]['ct'] > 0) ? true : false;
	}

	public function getPackageRelease($packageName)
	{
		try {
			$contents = file_get_contents('https://github.com/'.$packageName.'/releases.atom');
		} catch (\Exception $e) {
			return false;
		}

		$data = simplexml_load_string($contents);
		$item = $data->entry[0];

		$version = (string)$item->title;
		$author = $item->author;

		list($version, $majorVersion, $minorVersion, $patchVersion)
			= $this->parseVersion($version);

		$data = array(
			'name' => $packageName,
			'version' => $version,
			'major_version' => $majorVersion,
			'minor_version' => $minorVersion,
			'patch_version' => $patchVersion,
			'date_posted' => date('Y-m-d H:i:s', strtotime($item->updated)),
			'description' => strip_tags($item->content),
			'source' => 'https://github.com/'.$packageName,
			'author' => (string)$author->name
		);
		$this->insertRelease($data, $packageName, $version);
	}

	public function parseVersion($version)
	{
		$version = str_replace('v', '', $version);
		$parts = explode('.', $version);

		$majorVersion = $parts[0];
		$minorVersion = $parts[1];
		$patchVersion = (isset($parts[2]) == true) ? $parts[2] : '';

		return array(
			$version, $majorVersion,
			$minorVersion, $patchVersion
		);
	}

	public function insertRelease($data, $name, $version)
	{
		$columns = array();
		$bind = array();
		foreach ($data as $column => $d) {
			$columns[] = $column;
			$bind[] = ':'.$column;
		}
		// Be sure it doesn't already exist
		$sql = 'select id from releases where name = :name and version = :version';
		$result = $this->getPdo()->fetchAll($sql, array('name' => $name, 'version' => $version));

		if (count($result) == 0) {
			$sql = 'insert into releases ('.implode(',', $columns).', date_added) values ('.implode(',', $bind).', NOW())';
			$result = $this->getPdo()->perform($sql, $data);
			return true;
		}
		return false;
	}
}

?>