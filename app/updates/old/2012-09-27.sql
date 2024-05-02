
CREATE TABLE IF NOT EXISTS `jos_teamtimebpm_project_space` (
  `project_id` int(11) NOT NULL,
  `space_id` int(11) NOT NULL,
  KEY `project_id` (`project_id`,`space_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;