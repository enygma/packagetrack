CREATE TABLE `releases` (
	name VARCHAR(200),
	date_posted DATETIME,
	date_added DATETIME,
	description TEXT,
	source VARCHAR(200),
	author VARCHAR(100),
	version VARCHAR(100),
	major_version VARCHAR(100),
	minor_version VARCHAR(100),
	patch_version VARCHAR(100),
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

CREATE TABLE `feed_packages` (
	name VARCHAR(200),
	feed_id INT,
	date_added DATETIME,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

CREATE TABLE `feed` (
	hash TEXT,
	date_added DATETIME,
	last_view DATETIME,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

CREATE TABLE `queue` (
	package_url VARCHAR(200),
	package_name VARCHAR(200),
	package_description TEXT,
	package_source VARCHAR(200),
	package_author VARCHAR(200),
	date_added DATETIME,
	locked INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

CREATE TABLE `users` (
	username VARCHAR(200),
	password VARCHAR(100),
	email_address VARCHAR(200),
	status VARCHAR(10) default 'active',
	date_added DATETIME,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

CREATE TABLE `user_feeds` (
	user_id INT,
	feed_id INT,
	date_added DATETIME,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);