
ALTER TABLE  `jos_teamtimebpm_followprocess` ADD INDEX (  `user_id` );

ALTER TABLE  `jos_teamtimebpm_followspace` ADD INDEX (  `user_id` );

ALTER TABLE  `jos_teamtimebpm_followtemplate` ADD INDEX (  `user_id` );

ALTER TABLE  `jos_teamtimebpm_process` ADD INDEX (  `name` );

ALTER TABLE  `jos_teamtimebpm_project_space` ADD INDEX (  `space_id` );

ALTER TABLE  `jos_teamtimebpm_role` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamtimebpm_role` ADD INDEX (  `rate` );
ALTER TABLE  `jos_teamtimebpm_role` ADD INDEX (  `target_id` );
ALTER TABLE  `jos_teamtimebpm_role` ADD INDEX (  `rate_from_dotu` );
ALTER TABLE  `jos_teamtimebpm_role` ADD INDEX (  `user_id` );

ALTER TABLE  `jos_teamtimebpm_space` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamtimebpm_space` ADD INDEX (  `modified` );
ALTER TABLE  `jos_teamtimebpm_space` ADD INDEX (  `modified_by` );
ALTER TABLE  `jos_teamtimebpm_space` ADD INDEX (  `archived` );

ALTER TABLE  `jos_teamtimebpm_template` ADD INDEX (  `name` );

ALTER TABLE  `jos_teamtimebpm_todo` ADD INDEX (  `process_id` );

ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `target_id` );
ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `title` );
ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `num` );
ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `user_id` );
ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `log_id` );
ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `todo_id` );
ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `date` );
ALTER TABLE  `jos_teamtimecareer_statevector` ADD INDEX (  `skill_target_id` );

ALTER TABLE  `jos_teamtimecareer_targetvector` ADD INDEX (  `parent_id` );
ALTER TABLE  `jos_teamtimecareer_targetvector` ADD INDEX (  `title` );
ALTER TABLE  `jos_teamtimecareer_targetvector` ADD INDEX (  `num` );
ALTER TABLE  `jos_teamtimecareer_targetvector` ADD INDEX (  `hourprice` );
ALTER TABLE  `jos_teamtimecareer_targetvector` ADD INDEX (  `ordering` );
ALTER TABLE  `jos_teamtimecareer_targetvector` ADD INDEX (  `is_skill` );

ALTER TABLE  `jos_teamtimecareer_target_balance` ADD INDEX (  `user_id` );
ALTER TABLE  `jos_teamtimecareer_target_balance` ADD INDEX (  `num` );

ALTER TABLE  `jos_teamtimecareer_task_target` ADD INDEX (  `target_id` );

ALTER TABLE  `jos_teamtimecareer_todo_target` ADD INDEX (  `target_id` );

ALTER TABLE  `jos_teamtimeformals_formal` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamtimeformals_formal` ADD INDEX (  `project_id` );
ALTER TABLE  `jos_teamtimeformals_formal` ADD INDEX (  `doctype_id` );
ALTER TABLE  `jos_teamtimeformals_formal` ADD INDEX (  `price` );
ALTER TABLE  `jos_teamtimeformals_formal` ADD INDEX (  `created` );

ALTER TABLE  `jos_teamtimeformals_formaldata` ADD INDEX (  `variable_id` );
ALTER TABLE  `jos_teamtimeformals_formaldata` ADD INDEX (  `mdate` );
ALTER TABLE  `jos_teamtimeformals_formaldata` ADD INDEX (  `project_id` );
ALTER TABLE  `jos_teamtimeformals_formaldata` ADD INDEX (  `user_id` );

ALTER TABLE  `jos_teamtimeformals_template` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamtimeformals_template` ADD INDEX (  `type` );

ALTER TABLE  `jos_teamtimeformals_todo` ADD INDEX (  `mark_expenses` );
ALTER TABLE  `jos_teamtimeformals_todo` ADD INDEX (  `mark_hours_plan` );

ALTER TABLE  `jos_teamtimeformals_type` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamtimeformals_type` ADD INDEX (  `generator` );
ALTER TABLE  `jos_teamtimeformals_type` ADD INDEX (  `using_in` );

ALTER TABLE  `jos_teamtimeformals_variable` ADD INDEX (  `name` );
ALTER TABLE  `jos_teamtimeformals_variable` ADD INDEX (  `tagname` );
ALTER TABLE  `jos_teamtimeformals_variable` ADD INDEX (  `using_in` );

ALTER TABLE  `jos_teamtimeformals_variable_project` ADD INDEX (  `variable_id` );
ALTER TABLE  `jos_teamtimeformals_variable_user` ADD INDEX (  `variable_id` );