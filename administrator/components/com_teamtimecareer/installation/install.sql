
CREATE TABLE IF NOT EXISTS `#__teamtimecareer_statevector` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `num` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_id` int(11) DEFAULT NULL,
  `todo_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `skill_target_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`),
  KEY `title` (`title`),
  KEY `num` (`num`),
  KEY `user_id` (`user_id`),
  KEY `log_id` (`log_id`),
  KEY `todo_id` (`todo_id`),
  KEY `date` (`date`),
  KEY `skill_target_id` (`skill_target_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimecareer_targetvector` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `num` int(11) NOT NULL,
  `hourprice` int(11) NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  `is_skill` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `title` (`title`),
  KEY `num` (`num`),
  KEY `hourprice` (`hourprice`),
  KEY `ordering` (`ordering`),
  KEY `is_skill` (`is_skill`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimecareer_target_balance` (
  `target_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `num` float NOT NULL,
  UNIQUE KEY `target_id` (`target_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `num` (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimecareer_task_price` (
  `id` int(11) unsigned NOT NULL,
  `price` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimecareer_task_target` (
  `id` int(11) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimecareer_todo_target` (
  `id` int(11) unsigned NOT NULL,
  `target_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;