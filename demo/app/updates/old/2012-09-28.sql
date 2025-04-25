
ALTER TABLE  `jos_teamtimebpm_process` ADD  `project_id` INT NOT NULL AFTER  `is_started`;
ALTER TABLE  `jos_teamtimebpm_process` ADD INDEX (  `space_id` );
ALTER TABLE  `jos_teamtimebpm_process` ADD INDEX (  `project_id` );
ALTER TABLE  `jos_teamtimebpm_process` ADD INDEX (  `archived` );
ALTER TABLE  `jos_teamtimebpm_process` ADD INDEX (  `modified` );
ALTER TABLE  `jos_teamtimebpm_process` ADD INDEX (  `modified_by` );

ALTER TABLE  `jos_teamtimebpm_template` ADD  `project_id` INT NOT NULL AFTER  `space_id`;
ALTER TABLE  `jos_teamtimebpm_template` ADD INDEX (  `project_id` );
ALTER TABLE  `jos_teamtimebpm_template` ADD INDEX (  `space_id` );
ALTER TABLE  `jos_teamtimebpm_template` ADD INDEX (  `archived` );
ALTER TABLE  `jos_teamtimebpm_template` ADD INDEX (  `modified` );
ALTER TABLE  `jos_teamtimebpm_template` ADD INDEX (  `modified_by` );