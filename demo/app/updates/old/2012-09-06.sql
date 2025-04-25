
RENAME TABLE `jos_teamtimebpmn_followprocess` TO `jos_teamtimebpm_followprocess`;
RENAME TABLE `jos_teamtimebpmn_followspace` TO `jos_teamtimebpm_followspace`;
RENAME TABLE `jos_teamtimebpmn_followtemplate` TO `jos_teamtimebpm_followtemplate`;
RENAME TABLE `jos_teamtimebpmn_process` TO `jos_teamtimebpm_process`;
RENAME TABLE `jos_teamtimebpmn_processdiagram` TO `jos_teamtimebpm_processdiagram`;
RENAME TABLE `jos_teamtimebpmn_role` TO `jos_teamtimebpm_role`;
RENAME TABLE `jos_teamtimebpmn_space` TO `jos_teamtimebpm_space`;
RENAME TABLE `jos_teamtimebpmn_template` TO `jos_teamtimebpm_template`;
RENAME TABLE `jos_teamtimebpmn_templatediagram` TO `jos_teamtimebpm_templatediagram`;
RENAME TABLE `jos_teamtimebpmn_todo` TO `jos_teamtimebpm_todo`;

RENAME TABLE `jos_teamtimedotu_statevector` TO `jos_teamtimecareer_statevector`;
RENAME TABLE `jos_teamtimedotu_targetvector` TO `jos_teamtimecareer_targetvector`;
RENAME TABLE `jos_teamtimedotu_target_balance` TO `jos_teamtimecareer_target_balance`;
RENAME TABLE `jos_teamtimedotu_task_price` TO `jos_teamtimecareer_task_price`;
RENAME TABLE `jos_teamtimedotu_task_target` TO `jos_teamtimecareer_task_target`;
RENAME TABLE `jos_teamtimedotu_todo_target` TO `jos_teamtimecareer_todo_target`;

UPDATE `jos_components` SET
`admin_menu_link` = 'option=com_teamtimecareer',
`option` = 'com_teamtimecareer'
WHERE `option` = 'com_teamtimedotu';

UPDATE `jos_components` SET
`admin_menu_link` = 'option=com_teamtimebpm',
`option` = 'com_teamtimebpm'
WHERE `option` = 'com_teamtimebpmn';