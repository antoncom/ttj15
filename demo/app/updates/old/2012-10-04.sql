
RENAME TABLE  `jos_teamlog_log` TO  `jos_teamtime_log`;
ALTER TABLE  `jos_teamtime_log` ENGINE = INNODB;

RENAME TABLE  `jos_teamlog_project` TO  `jos_teamtime_project`;
ALTER TABLE  `jos_teamtime_project` ENGINE = INNODB;

RENAME TABLE  `jos_teamlog_project_user` TO  `jos_teamtime_project_user`;
ALTER TABLE  `jos_teamtime_project_user` ENGINE = INNODB;

RENAME TABLE  `jos_teamlog_task` TO  `jos_teamtime_task`;
ALTER TABLE  `jos_teamtime_task` ENGINE = INNODB;

RENAME TABLE  `jos_teamlog_todo` TO  `jos_teamtime_todo`;
ALTER TABLE  `jos_teamtime_todo` ENGINE = INNODB;

RENAME TABLE  `jos_teamlog_type` TO  `jos_teamtime_type`;
ALTER TABLE  `jos_teamtime_type` ENGINE = INNODB;

RENAME TABLE  `jos_teamlog_userdata` TO  `jos_teamtime_userdata`;
ALTER TABLE  `jos_teamtime_userdata` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_process` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_processdiagram` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_project_space` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_role` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_space` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_template` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_templatediagram` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimebpm_todo` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimecareer_statevector` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimecareer_targetvector` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimecareer_target_balance` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimecareer_task_price` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimecareer_task_target` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimecareer_todo_target` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_formal` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_formaldata` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_template` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_todo` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_type` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_variable` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_variable_project` ENGINE = INNODB;

ALTER TABLE  `jos_teamtimeformals_variable_user` ENGINE = INNODB;
