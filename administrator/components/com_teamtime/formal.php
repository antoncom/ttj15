<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

if (!class_exists("FormalModelFormal")) {

	class FormalModelFormal extends JModel {

		/**
		 * Table
		 *
		 * @var array
		 */
		var $_table = 'formal';
		/**
		 * Model item id
		 *
		 * @var int
		 */
		var $_id = null;
		/**
		 * Model item data
		 *
		 * @var array
		 */
		var $_data = null;

		/**
		 * Constructor
		 *
		 */
		function __construct() {
			parent::__construct();

			$array = JRequest::getVar('cid', array(0), '', 'array');
			$edit = JRequest::getVar('edit', true);

			if ($edit) {
				$this->setId((int) $array[0]);
			}
		}

		/**
		 * Method to set the model item identifier
		 *
		 * @access	public
		 * @param	int identifier
		 */
		function setId($id) {
			$this->_id = $id;
			$this->_data = null;
		}

		/**
		 * Method to get model item data
		 *
		 */
		function &getData() {
			if (empty($this->_data)) {
				$row = & $this->getTable($this->_table);

				// load the row from the db table
				if ($this->_id) {
					$row->load($this->_id);
				}

				// set defaults, if new
				if ($row->id == 0) {
					
				}

				$this->_data = & $row;
			}

			return $this->_data;
		}

		/**
		 * Method to store the model item
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function store($data) {
			$row = & $this->getTable($this->_table);

			// bind the form fields
			if (!$row->bind($data)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// check if model item data is valid
			if (!$row->check()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// store model item to the database
			if (!$row->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			$this->_data = $row;

			return true;
		}

		/**
		 * Method to remove a model item
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function delete($cid = array()) {
			$table = & $this->getTable($this->_table);

			if (count($cid)) {
				JArrayHelper::toInteger($cid);
				$cids = implode(',', $cid);
				$query = 'DELETE FROM ' . $table->getTableName()
						. ' WHERE id IN (' . $cids . ')';
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}

			return true;
		}

		function generateContent($params, $using_in = "project") {
			$db = & JFactory::getDBO();

			$from_period = $params["from_period"];
			$until_period = $params["until_period"];
			$project_id = (int) $params["project_id"];
			$template_id = (int) $params["doctype_id"];

			//get template data
			$sql = "select b.name as template_name, b.description as template_content,
				c.name as doctype_name, c.generator
				from #__teamtimeformals_template as b
				left join #__teamtimeformals_type as c on b.type = c.id
				where b.id = {$template_id}";
			$db->setQuery($sql);
			$row_template = $db->loadObject();

			$total_sum_price = 0;
			$result_content = "";
			$generator_fname = $path = JPATH_ROOT .
					"/administrator/components/com_teamtimeformals/assets/generators/" .
					$row_template->generator . ".php";
			if (!file_exists($generator_fname)) {
				return array("File $generator_fname not found");
			}

			$tpl = new HTML_Template_IT("");
			$tpl->setTemplate($row_template->template_content, true, true);

			//select and set main variables
			if ($using_in == "project") {
				$sql = "select b.tagname, b.name, b.defaultval, a.content, a.mdate
					from #__teamtimeformals_variable as b
					left join #__teamtimeformals_formaldata as a on b.id = a.variable_id
					where a.project_id = {$project_id} or a.project_id is null
					order by a.mdate desc";
			}
			else if ($using_in == "user") {
				$sql = "select b.tagname, b.name, b.defaultval, a.content, a.mdate
					from #__teamtimeformals_variable as b
					left join #__teamtimeformals_formaldata as a on b.id = a.variable_id
					where a.user_id = {$project_id} or a.user_id is null
					order by a.mdate desc";
			}
			else
				$sql = "";

			$db->setQuery($sql);
			$rows_variables = $db->loadObjectList();
			$variables = array();
			$variables_names = array();
			foreach ($rows_variables as $row) {
				if (substr($row->tagname, 0, 9) == "user_tax_") { //store as array with key by date
					if (trim($row->content) != "")
						$v = $row->content;
					else
						$v = $row->defaultval;

					if (!isset($variables[$row->tagname]))
						$variables[$row->tagname] = array();

					if ($v != "")
						$variables[$row->tagname][$row->mdate] = $v;
				}
				else {
					//use only last variable value
					if (isset($variables[$row->tagname]))
						continue;

					if (trim($row->content) != "")
						$variables[$row->tagname] = $row->content;
					else
						$variables[$row->tagname] = $row->defaultval;
				}

				$variables_names[$row->tagname] = $row->name;
			}

			//process tax variables
			foreach (range(1, 10) as $v) {
				if (!isset($variables["user_tax_" . $v]) || sizeof($variables["user_tax_" . $v]) == 0)
					unset($variables["user_tax_" . $v]);
				else {
					$current_date_tax = strtotime(date("Y-m-01", strtotime($from_period)));
					//set tax N for selected date
					foreach ($variables["user_tax_" . $v] as $d => $tax)
						if (strtotime($d) <= $current_date_tax) {
							$variables["current_user_tax_" . $v] = $tax;
							break;
						}
				}
			}

			//set other main variables
			$variables["start_date_short"] = JHTML::_('date', $from_period, "%d%m%y");
			$variables["end_date_short"] = JHTML::_('date', $until_period, "%d%m%y");

			$variables["start_date_middle"] = JHTML::_('date', $from_period, "%d%m%Y");
			$variables["end_date_middle"] = JHTML::_('date', $until_period, "%d%m%Y");

			$date_str = explode(" ", JHTML::_('date', $from_period, "%d %m %Y"));
			$date_str[1] = JText::_("STR_MONTH" . (int) $date_str[1]);
			$date_str = implode(" ", $date_str);
			$variables["start_date_long"] = $date_str;

			$date_str = explode(" ", JHTML::_('date', $until_period, "%d %m %Y"));
			$date_str[1] = JText::_("STR_MONTH" . (int) $date_str[1]);
			$date_str = implode(" ", $date_str);
			$variables["end_date_long"] = $date_str;

			$now = date("Y-m-d");
			$variables["todate_short"] = JHTML::_('date', $now, "%d%m%y");
			$variables["todate_middle"] = JHTML::_('date', $now, "%d%m%Y");

			$variables["todate_day"] = JHTML::_('date', $now, "%d");
			$variables["todate_month"] = JHTML::_('date', $now, "%m");
			$variables["todate_year"] = JHTML::_('date', $now, "%Y");
			$variables["todate_of_month"] = JText::_("STR_MONTH" . (int) JHTML::_('date', $now, "%m"));

			$now_str = explode(" ", JHTML::_('date', $now, "%d %m %Y"));
			$now_str[1] = JText::_("STR_MONTH" . (int) $now_str[1]);
			$now_str = implode(" ", $now_str);
			$variables["todate_long"] = $now_str;

			$variables["start_date_short_pointed"] = JHTML::_('date', $from_period, "%d.%m.%y");
			$variables["end_date_short_pointed"] = JHTML::_('date', $until_period, "%d.%m.%y");

			$variables["start_date_middle_pointed"] = JHTML::_('date', $from_period, "%d.%m.%Y");
			$variables["end_date_middle_pointed"] = JHTML::_('date', $until_period, "%d.%m.%Y");

			$variables["current_month_YYYY"] = JHTML::_('date', $now, "%B %Y");
			$next_month = date("Y-m-d", mktime(0, 0, 0, date("n") + 1, date("j"), date("Y")));
			$variables["next_month_YYYY"] = JHTML::_('date', $next_month, "%B %Y");

			$variables["doc_counter"] = $this->get_doc_count($template_id) + 1;

			$data = array();
			$rows_todos = array();
			if ($using_in == "project") {
				//todos list
				$sql = "select a.*, b.name as type_name,
						c.rate as project_hourly_rate, c.dynamic_rate as project_dynamic_rate,
						d.mark_expenses, d.mark_hours_plan, rd.repeat_date,
						rf.parent_id as parent_id
					from #__teamlog_todo as a
					LEFT JOIN #__teamtime_todo_repeatparams AS p ON a.id = p.todo_id
					LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id
					left join #__teamlog_type as b on a.type_id = b.id
					left join #__teamlog_project as c on a.project_id = c.id
					left join #__teamtimeformals_todo as d on a.id = d.todo_id
					left join #__teamtime_todo_ref as rf on a.id = rf.todo_id
					where a.project_id = {$project_id} and
						if(rd.todo_id is null,
							a.created >= " . $db->Quote($db->getEscaped("$from_period 00:00:00", true), false) . "
								and a.created <= " . $db->Quote($db->getEscaped("$until_period 23:59:59", true), false) . ",
							rd.repeat_date >= " . $db->Quote($db->getEscaped("$from_period 00:00:00", true), false) . "
								and rd.repeat_date <= " . $db->Quote($db->getEscaped("$until_period 23:59:59", true), false) . ")
						and (d.mark_expenses = 1 or d.mark_hours_plan = 1)
						and rf.parent_id is null
					/*group by a.id*/
					order by a.created";

				//error_log($db->replacePrefix($sql));

				$db->setQuery($sql);
				$rows_todos = $db->loadObjectList();

				$todo_model = new TodoModelTodo();

				$data = array();
				foreach ($rows_todos as $i => $row) {
					if ($row->mark_hours_plan == 0)
						$row->project_hourly_rate = 0;

					if ($row->mark_expenses == 0)
						$row->costs = 0;

					//process $row for group data
					$rows_todos[$i] = $todo_model->calcfields_for_parent_todo($row->id, $row);

					//set current repeat date if exists
					if ($row->repeat_date)
						$rows_todos[$i]->created = $row->repeat_date;

					if (!isset($data[$row->type_id]))
						$data[$row->type_id] = array();

					$data[$row->type_id][] = $row;
				}
			}
			else if ($using_in == "user") {
				$sql = "select a.*, b.name as type_name, c.name as project_name
					from #__teamlog_todo as a
					left join #__teamlog_type as b on a.type_id = b.id
					left join #__teamlog_project as c on a.project_id = c.id
					LEFT JOIN #__teamtime_todo_repeatparams AS p ON a.id = p.todo_id
					LEFT JOIN #__teamtime_todo_repeatdate AS rd ON a.id = rd.todo_id
					where a.user_id = {$project_id} and
            if(rd.todo_id is null,
							a.created >= " . $db->Quote($db->getEscaped("$from_period 00:00:00", true), false) . "
								and a.created <= " . $db->Quote($db->getEscaped("$until_period 23:59:59", true), false) . ",
							rd.repeat_date >= " . $db->Quote($db->getEscaped("$from_period 00:00:00", true), false) . "
								and rd.repeat_date <= " . $db->Quote($db->getEscaped("$until_period 23:59:59", true), false) . ")"
						. " order by a.created";
				$db->setQuery($sql);
				$rows_todos = $db->loadObjectList();

				//error_log($db->replacePrefix($sql));

				$year_from_period = date("Y-01-01", strtotime($from_period));
				$year_until_period = date("Y-12-31", strtotime($until_period));
				$year_limit_period = date("Y-m-31"); //limit for current year-month
				$sql_year = "select a.*, b.name as type_name, c.name as project_name
					from #__teamlog_todo as a
					left join #__teamlog_type as b on a.type_id = b.id
					left join #__teamlog_project as c on a.project_id = c.id
					where a.user_id = {$project_id}" .
						" and a.created >= " . $db->Quote($db->getEscaped("$year_from_period 00:00:00", true), false) .
						//" and a.created <= " . $db->Quote($db->getEscaped("$year_until_period 23:59:59", true), false) .
						" and a.created <= " . $db->Quote($db->getEscaped("$year_limit_period 23:59:59", true), false) .
						" order by a.created";
				$db->setQuery($sql_year);
				$rows_todos_year = $db->loadObjectList();

				$sql_year_data = "select distinct date_format(a.created, '%Y') as tmp_year,
						date_format(a.created, '%m') as tmp_m
					from #__teamlog_todo as a
					left join #__teamlog_type as b on a.type_id = b.id
					left join #__teamlog_project as c on a.project_id = c.id
					where a.user_id = {$project_id}
						and a.created <= " . $db->Quote($db->getEscaped("$year_limit_period 23:59:59", true), false) .
						" order by a.created";
				$db->setQuery($sql_year_data);
				$tmp_rows = $db->loadObjectList();
				$rows_year_data = array();
				foreach ($tmp_rows as $row) {
					if ($row->tmp_year == '0000')
						continue;

					if (!isset($rows_year_data[$row->tmp_year]))
						$rows_year_data[$row->tmp_year] = array();

					$rows_year_data[$row->tmp_year][] = $row->tmp_m;
				}

				$data = array();

				$total_sum_price = 0;
				foreach ($rows_todos as $row) {
					$k = $row->project_name . '-' . $row->type_name;

					if (!isset($data[$k]))
						$data[$k] = array();

					$data[$k][] = $row;
					$total_sum_price += $row->hours_plan * $row->hourly_rate;
				}
				ksort($data); //sort by project/type

				$variables["total_user_sum"] = $total_sum_price;
				$price_str = TeamTime::_("num2curr", $total_sum_price);
				$variables["total_user_sum_string"] = $price_str[1];
				$variables["total_user_sum_string_rest"] = $price_str[2];

				$db->setQuery("select * from #__users where id = {$project_id}");
				$tmp_row = $db->loadObject();
				$variables["for_user_name"] = $tmp_row->name;
			}

			include($generator_fname);

			return array($result_content, $total_sum_price);
		}

		function get_doc_count($template_id) {
			$db = & JFactory::getDBO();

			$db->setQuery("select count(*) as num from #__teamtimeformals_formal
				where doctype_id = " . (int) $template_id);
			$row = $db->loadObject();

			return $row ? $row->num : 0;
		}

		function get_template_id_by_generator($generator) {
			$db = & JFactory::getDBO();

			$generator = $db->Quote($generator);
			$db->setQuery("select b.* from #__teamtimeformals_type as a
				left join #__teamtimeformals_template as b on a.id = b.type
				where a.generator = $generator
				limit 1");
			$row = $db->loadObject();

			return $row->id;
		}

	}

}