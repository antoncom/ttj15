CREATE TABLE IF NOT EXISTS `#__teamtime_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `duration` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `ended` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `datepause` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sumpause` int(11) NOT NULL DEFAULT '0',
  `money` float NOT NULL DEFAULT '0',
  `todo_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type_id` (`type_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`),
  KEY `duration` (`duration`),
  KEY `date` (`date`),
  KEY `created` (`created`),
  KEY `ended` (`ended`),
  KEY `modified` (`modified`),
  KEY `datepause` (`datepause`),
  KEY `money` (`money`),
  KEY `todo_id` (`todo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__teamtime_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `rate` float NOT NULL,
  `dynamic_rate` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `state` (`state`),
  KEY `rate` (`rate`),
  KEY `dynamic_rate` (`dynamic_rate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__teamtime_project_user` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `project_id` (`project_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtime_repeat_todo_ref` (
  `todo_id` int(11) NOT NULL,
  `repeating_history` enum('weekly','monthly','yearly') DEFAULT NULL,
  UNIQUE KEY `todo_id` (`todo_id`),
  KEY `repeating_history` (`repeating_history`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtime_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `type_id` int(11) NOT NULL,
  `rate` int(5) NOT NULL DEFAULT '360',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `project_id` (`project_id`),
  KEY `name` (`name`),
  KEY `state` (`state`),
  KEY `rate` (`rate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__teamtime_todo` (
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `hours_plan` float NOT NULL,
  `hours_fact` float NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created` datetime NOT NULL,
  `modified` date NOT NULL,
  `modified_by` int(11) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `color` varchar(200) NOT NULL,
  `isalldayevent` smallint(6) NOT NULL,
  `hourly_rate` float NOT NULL,
  `costs` float NOT NULL,
  `current_repeat_date` datetime NOT NULL,
  `is_parent` tinyint(1) NOT NULL,
  `selected` tinyint(1) NOT NULL,
  `showskills` tinyint(1) NOT NULL,
  `is_autotodo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `title` (`title`),
  KEY `hours_plan` (`hours_plan`),
  KEY `hours_fact` (`hours_fact`),
  KEY `state` (`state`),
  KEY `created` (`created`),
  KEY `modified` (`modified`),
  KEY `modified_by` (`modified_by`),
  KEY `task_id` (`task_id`),
  KEY `project_id` (`project_id`),
  KEY `type_id` (`type_id`),
  KEY `isalldayevent` (`isalldayevent`),
  KEY `hourly_rate` (`hourly_rate`),
  KEY `costs` (`costs`),
  KEY `current_repeat_date` (`current_repeat_date`),
  KEY `is_parent` (`is_parent`),
  KEY `selected` (`selected`),
  KEY `showskills` (`showskills`),
  KEY `is_autotodo` (`is_autotodo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__teamtime_todo_ref` (
  `todo_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  UNIQUE KEY `todo_id` (`todo_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtime_todo_repeatdate` (
  `todo_id` int(11) NOT NULL,
  `repeat_date` datetime NOT NULL,
  UNIQUE KEY `todo_id` (`todo_id`,`repeat_date`),
  KEY `repeat_date` (`repeat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtime_todo_repeatparams` (
  `todo_id` int(11) NOT NULL,
  `repeat_mode` enum('weekly','monthly','yearly') NOT NULL,
  `repeat_mon` tinyint(1) NOT NULL,
  `repeat_tue` tinyint(1) NOT NULL,
  `repeat_wed` tinyint(1) NOT NULL,
  `repeat_thu` tinyint(1) NOT NULL,
  `repeat_fri` tinyint(1) NOT NULL,
  `repeat_sat` tinyint(1) NOT NULL,
  `repeat_sun` tinyint(1) NOT NULL,
  `repeat_interval` int(11) NOT NULL,
  `end_date` datetime NOT NULL,
  `start_date` datetime NOT NULL,
  UNIQUE KEY `todo_id` (`todo_id`),
  KEY `repeat_mode` (`repeat_mode`),
  KEY `end_date` (`end_date`),
  KEY `start_date` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtime_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `rate` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rate` (`rate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__teamtime_userdata` (
  `user_id` int(11) NOT NULL,
  `state_description` varchar(255) NOT NULL,
  `state_modified` datetime NOT NULL,
  `send_msg` tinyint(1) NOT NULL,
  `hour_price` float NOT NULL,
  `hideforother` tinyint(1) NOT NULL,
  `salary` float NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `state_description` (`state_description`),
  KEY `state_modified` (`state_modified`),
  KEY `hideforother` (`hideforother`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
