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
}

?>