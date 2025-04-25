CREATE TABLE IF NOT EXISTS `jos_teamtimebpmn_followspace` (
  `space_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow` tinyint(1) NOT NULL,
  UNIQUE KEY `space_id` (`space_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jos_teamtimebpmn_followprocess` (
  `process_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow` tinyint(1) NOT NULL,
  UNIQUE KEY `process_id` (`process_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jos_teamtimebpmn_followtemplate` (
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `follow` tinyint(1) NOT NULL,
  UNIQUE KEY `template_id` (`template_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
