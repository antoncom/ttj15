
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `user_id` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `type_id` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `project_id` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `task_id` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `duration` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `date` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `created` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `ended` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `modified` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `datepause` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `money` );
ALTER TABLE  `jos_teamlog_log` ADD INDEX (  `todo_id` );

ALTER TABLE  `jos_teamlog_project` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamlog_project` ADD INDEX (  `state` );
ALTER TABLE  `jos_teamlog_project` ADD INDEX (  `rate` );
ALTER TABLE  `jos_teamlog_project` ADD INDEX (  `dynamic_rate` );

ALTER TABLE  `jos_teamlog_project_user` ADD INDEX (  `user_id` );

ALTER TABLE  `jos_teamlog_task` ADD INDEX (  `project_id` );
ALTER TABLE  `jos_teamlog_task` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamlog_task` ADD INDEX (  `state` );
ALTER TABLE  `jos_teamlog_task` ADD INDEX (  `rate` );

ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `user_id` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `title` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `hours_plan` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `hours_fact` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `state` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `created` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `modified` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `modified_by` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `task_id` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `project_id` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `type_id` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `isalldayevent` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `hourly_rate` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `costs` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `current_repeat_date` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `is_parent` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `selected` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `showskills` );
ALTER TABLE  `jos_teamlog_todo` ADD INDEX (  `is_autotodo` );

ALTER TABLE  `jos_teamlog_type` ADD INDEX (  `rate` );

ALTER TABLE  `jos_teamlog_userdata` ADD INDEX (  `state_description` );
ALTER TABLE  `jos_teamlog_userdata` ADD INDEX (  `state_modified` );
ALTER TABLE  `jos_teamlog_userdata` ADD INDEX (  `hideforother` );

ALTER TABLE  `jos_teamtime_repeat_todo_ref` ADD INDEX (  `repeating_history` );

ALTER TABLE  `jos_teamtime_todo_ref` ADD INDEX (  `parent_id` );

ALTER TABLE  `jos_teamtime_todo_repeatdate` ADD INDEX (  `repeat_date` );

ALTER TABLE  `jos_teamtime_todo_repeatparams` ADD INDEX (  `repeat_mode` );
ALTER TABLE  `jos_teamtime_todo_repeatparams` ADD INDEX (  `end_date` );
ALTER TABLE  `jos_teamtime_todo_repeatparams` ADD INDEX (  `start_date` );
