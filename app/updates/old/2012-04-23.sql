CREATE TABLE IF NOT EXISTS `jos_teamtimebpmn_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `archived` enum('active','archived') NOT NULL,
  `space_id` int(11) NOT NULL,  
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `jos_teamtimebpmn_templatediagram` (
  `template_id` int(11) NOT NULL,
  `data` text NOT NULL,
  UNIQUE KEY `template_id` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;