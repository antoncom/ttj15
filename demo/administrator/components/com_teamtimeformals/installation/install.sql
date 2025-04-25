CREATE TABLE IF NOT EXISTS `#__teamtimeformals_formal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  `doctype_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `content` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `project_id` (`project_id`),
  KEY `doctype_id` (`doctype_id`),
  KEY `price` (`price`),
  KEY `created` (`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimeformals_formaldata` (
  `variable_id` int(11) NOT NULL,
  `mdate` date NOT NULL,
  `content` text NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `variable_id` (`variable_id`,`project_id`,`user_id`,`mdate`),
  KEY `variable_id_2` (`variable_id`),
  KEY `mdate` (`mdate`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimeformals_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimeformals_todo` (
  `todo_id` int(11) NOT NULL,
  `mark_expenses` tinyint(1) NOT NULL,
  `mark_hours_plan` tinyint(1) NOT NULL,
  PRIMARY KEY (`todo_id`),
  KEY `mark_expenses` (`mark_expenses`),
  KEY `mark_hours_plan` (`mark_hours_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimeformals_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `generator` varchar(255) NOT NULL,
  `using_in` enum('project','user','system') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `generator` (`generator`),
  KEY `using_in` (`using_in`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimeformals_variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tagname` varchar(255) NOT NULL,
  `xsize` int(5) NOT NULL,
  `ysize` int(5) NOT NULL,
  `description` text NOT NULL,
  `defaultval` text NOT NULL,
  `using_in` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `tagname` (`tagname`),
  KEY `using_in` (`using_in`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__teamtimeformals_variable_project` (
  `project_id` int(11) NOT NULL,
  `variable_id` int(11) NOT NULL,
  UNIQUE KEY `project_id` (`project_id`,`variable_id`),
  KEY `variable_id` (`variable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__teamtimeformals_variable_user` (
  `user_id` int(11) NOT NULL,
  `variable_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`variable_id`),
  KEY `variable_id` (`variable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `#__teamtimeformals_type` (`id`, `name`, `generator`, `using_in`) VALUES
(1, 'Смета', 'statement', 'project'),
(2, 'Счет', 'invoice', 'project'),
(3, 'Акт', 'acceptcert', 'project'),
(4, 'Вознаграждение', 'earnings', 'user'),
(5, 'Кассовый ордер', 'disbursement-order', 'user'),
(6, 'Договор', 'contract', 'user'),
(7, 'Акт об оказанных услугах', 'services_rendered', 'user'),
(8, 'ТЗ', 'requirements', 'system'),
(9, 'План работ сотрудника на неделю', 'workplanuser', 'user'),
(10, 'План работ по проекту на неделю', 'workplanproject', 'project');

