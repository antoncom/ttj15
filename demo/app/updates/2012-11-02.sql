
CREATE TABLE IF NOT EXISTS `jos_teamtimebpm_processlink` (
  `parent_id` int(11) NOT NULL,
  `process_id` int(11) NOT NULL,
  UNIQUE KEY `parent_id_2` (`parent_id`,`process_id`),
  KEY `parent_id` (`parent_id`),
  KEY `process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
