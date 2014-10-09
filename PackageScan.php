<?php

class PackageScan
{
	/**
	 * Aura SQL ExtendedPdo instance (PDO)
	 * @var \Aura\Sql\ExtendedPdo
	 */
	private $pdo;

	/**
	 * Init the object and set the PDO instance
	 *
	 * @param \Aura\Sql\ExtendedPdo $pdo Aura SQL instance
	 */
	public function __construct(\Aura\Sql\ExtendedPdo $pdo)
	{
		$this->setPdo($pdo);
	}

	/**
	 * Set PDO instance
	 *
	 * @param \Aura\Sql\ExtendedPdo $pdo Aura SQL instance
	 */
	public function setPdo(\Aura\Sql\ExtendedPdo $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Get the current PDO instance
	 *
	 * @return \Aura\Sql\ExtendedPdo instance
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Insert the feed data
	 *
	 * @param string $hash Unique hash to describe the feed
	 * @return integer Insert ID of result
	 */
	public function insertFeed($hash)
	{
		$pdo = $this->getPdo();
		$sql = 'insert into feed (hash, date_added) values (:hash, NOW())';
		$result = $this->getPdo()->perform($sql, array('hash' => $hash));
		return $pdo->lastInsertId();
	}

	/**
	 * Inset the feed package relational data
	 *
	 * @param array $data Feed data to insert
	 * @return boolean Result of insert
	 */
	public function insertFeedPackage($data)
	{
		$sql = 'insert into feed_packages (name, feed_id, date_added) values (:name, :feedId, NOW())';
		$result = $this->getPdo()->perform($sql, $data);
		return $result;
	}

	/**
	 * Get the hash data from the feed by ID
	 *
	 * @param integer $feedId Feed ID to locate
	 * @return array Hash data
	 */
	public function getHashByFeedId($feedId)
	{
		$sql = 'select hash from feed where id = :id';
		$result = $this->getPdo()->fetchAll($sql, array('id' => $feedId));
		return $result[0]['hash'];
	}

	/**
	 * Update the "last viewed" value of a feed by hash
	 *
	 * @param string $hash Feed unique hash
	 * @return boolean Result of update
	 */
	public function updateLastViewed($hash)
	{
		$sql = 'update feed set last_view = NOW() where hash = :hash';
		return $this->getPdo()->perform($sql, array('hash' => $hash));
	}

	/**
	 * Get the packages for the given feed hash
	 *
	 * @param string $hash Given hash
	 * @return array Feed release results
	 */
	public function getFeedPackages($hash)
	{
		$sql = 'select f.id, fp.name from feed f, feed_packages fp where f.hash = :hash'
		.' and f.id = fp.feed_id order by f.date_added desc';

		$results = $this->getPdo()->fetchAll($sql, array('hash' => $hash));
		return $results;
	}

	/**
	 * Get the releases for the given package name(s)
	 *
	 * @param array $packages Set of package names
	 * @return array Package records if found
	 */
	public function getReleaseByPackage(array $packages)
	{
		$pdo = $this->getPdo();
		$sql = 'select * from releases where lower(name) in ('.$pdo->quote($packages).') order by date_posted desc';
		$results = $pdo->fetchAll($sql);
		return $results;
	}

	/**
	 * Check to see if a package already exists in the releases table
	 *
	 * @param string $packageName Package name
	 * @return boolean True if found, false if not
	 */
	public function packageExists($packageName)
	{
		$sql = 'select count(id) as ct from releases where lower(name) = :name';
		$results = $this->getPdo()->fetchAll($sql, array('name' => $packageName));
		return ($results[0]['ct'] > 0) ? true : false;
	}

	/**
	 * Parse the version into major, minor and patch values
	 *
	 * @param string $version Full version value
	 * @return array Version information broken up
	 */
	public function parseVersion($version)
	{
		$version = str_replace('v', '', $version);
		$parts = explode('.', $version);

		$majorVersion = $parts[0];
		$minorVersion = (isset($parts[1]) ? $parts[1] : '';
		$patchVersion = (isset($parts[2]) == true) ? $parts[2] : '';

		return array(
			$version, $majorVersion,
			$minorVersion, $patchVersion
		);
	}

	/**
	 * Insert the release information
	 *
	 * @param array $data Release data
	 * @param string $name Package name
	 * @param string $version Package release version
	 * @return boolean True on insert, false if already found
	 */
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

	/**
	 * Add the given package to the queue list
	 *
	 * @param object $package Package object (from the Packagist feed)
	 */
	public function addToQueue($package)
	{
		$author = (isset($package->authors)) ? $package->authors[0]->name : '';
		$description = (isset($package->description)) ? $package->description : '';

		$data = array(
			'package_url' => $package->source->url,
			'package_name' => $package->name,
			'package_description' => $description,
			'package_source' => str_replace('.git', '', $package->source->url),
			'package_author' => $author
		);
		$bind = array();
		$columns = array();
		foreach ($data as $column => $value) {
			$columns[] = $column;
			$bind[] = ':'.$column;
		}

		$sql = 'insert into queue ('.implode(',', $columns).', date_added, locked) values ('.implode(',', $bind).', NOW(), 0)';
		$result = $this->getPdo()->perform($sql, $data);
	}

	/**
	 * Get the current data from the queue for fetching
	 *
	 * @param integer $limit A limit on the number of queue items to fetch
	 * @return array Found queue records
	 */
	public function getFromQueue($limit = 20)
	{
		$sql = 'select * from queue where locked = 0 limit '.$limit;
		$result = $this->getPdo()->fetchAll($sql);
		return $result;
	}

	/**
	 * Remove the package from the queue by name
	 *
	 * @param string $packageName Package name (ex. "psecio/iniscan")
	 */
	public function removeFromQueue($packageName)
	{
		$sql = 'delete from queue where package_name = :packageName';
		$this->getPdo()->perform($sql, array('packageName' => $packageName));
	}
}

?>