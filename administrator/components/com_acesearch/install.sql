# ---------------
# AceSearch SQL Installation
# ---------------
DROP TABLE IF EXISTS `#__acesearch_extensions`;
DROP TABLE IF EXISTS `#__acesearch_filters`;
DROP TABLE IF EXISTS `#__acesearch_search_results`;

CREATE TABLE IF NOT EXISTS `#__acesearch_extensions`(
	`id` INT(255) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`extension` VARCHAR(255) NOT NULL DEFAULT '',
	`params` TEXT NOT NULL DEFAULT '',
	`ordering` INT(255) NOT NULL DEFAULT '1',
	`client` INT(2) NOT NULL DEFAULT '0',
	PRIMARY KEY(`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__acesearch_filters`(
	`id` INT(255) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`author` VARCHAR(255) NOT NULL DEFAULT '',
	`extension` VARCHAR(255) NOT NULL DEFAULT '',
	`category` INT(255) NOT NULL DEFAULT '-1',
	`published` INT(1) NOT NULL DEFAULT '0',
	`date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY(`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__acesearch_search_results`(
	`id` INT(255) NOT NULL AUTO_INCREMENT,
	`keyword` VARCHAR(255) NOT NULL DEFAULT '',
	`extension` VARCHAR(255) NOT NULL DEFAULT '',
	`search_result` INT(255) NOT NULL DEFAULT '0',
	`search_count` INT(255) NOT NULL DEFAULT '0',
	`search_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY(`id`)
) DEFAULT CHARSET=utf8;