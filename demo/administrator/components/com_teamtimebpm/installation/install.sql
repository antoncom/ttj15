
CREATE TABLE IF NOT EXISTS `#__teamtimebpm_followprocess` (
  `process_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow` tinyint(1) NOT NULL,
  UNIQUE KEY `process_id` (`process_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_followspace` (
  `space_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow` tinyint(1) NOT NULL,
  UNIQUE KEY `space_id` (`space_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_followtemplate` (
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow` tinyint(1) NOT NULL,
  UNIQUE KEY `template_id` (`template_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `archived` enum('active','archived') NOT NULL,
  `space_id` int(11) NOT NULL,
  `is_started` tinyint(1) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `space_id` (`space_id`),
  KEY `project_id` (`project_id`),
  KEY `archived` (`archived`),
  KEY `modified` (`modified`),
  KEY `modified_by` (`modified_by`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_processdiagram` (
  `process_id` int(11) NOT NULL,
  `data` mediumtext NOT NULL,
  UNIQUE KEY `process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_project_space` (
  `project_id` int(11) NOT NULL,
  `space_id` int(11) NOT NULL,
  KEY `project_id` (`project_id`,`space_id`),
  KEY `space_id` (`space_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `rate` int(5) NOT NULL,
  `target_id` int(11) NOT NULL,
  `rate_from_dotu` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `rate` (`rate`),
  KEY `target_id` (`target_id`),
  KEY `rate_from_dotu` (`rate_from_dotu`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_space` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `archived` enum('active','archived') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `modified` (`modified`),
  KEY `modified_by` (`modified_by`),
  KEY `archived` (`archived`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `archived` enum('active','archived') NOT NULL,
  `space_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `space_id` (`space_id`),
  KEY `archived` (`archived`),
  KEY `modified` (`modified`),
  KEY `modified_by` (`modified_by`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_templatediagram` (
  `template_id` int(11) NOT NULL,
  `data` mediumtext NOT NULL,
  UNIQUE KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimebpm_todo` (
  `todo_id` int(11) NOT NULL,
  `process_id` int(11) NOT NULL,
  PRIMARY KEY (`todo_id`),
  KEY `process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;