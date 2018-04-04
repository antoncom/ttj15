
var TeamlogObj = null;

var Teamlog = new Class({

	initialize: function(url, options){
		this.setOptions({
			msgDeletelog: 'Are you sure you want to delete the log?'
		}, options);

		this.url       = url;
		this.state     = $('state');
		this.projectId = $('project-id');
		this.taskId = $('task-id');
		this.todoId = $('todo-id');
		//		this.hoursId = $('hours');/////////////////////////////////////////////////////
		//		this.minutesId = $('minutes');/////////////////////////////////////////////////
		this.logDelete = $$('#yoo-teamlog div.user-log ul.log span.delta a');

		this.todos_str = "Todos: ";
	},

	attachEvents: function(opts) {
		new Tips($$('ul.log span.tooltip'));

		// logs
		new Observer(this.state, this.updateState.bind(this), {
			delay: 1000
		});
		this.projectId.addEvent('change', this.getTasks.bind(this));
		this.logDelete.addEvent('click', this.confirmDelete.bind(this));

		if ($('team-log')) this.getTeamLog.bind(this).periodical(350000);

		// todos
		var todo = jQuery("#yoo-teamlog div.todos")
		var trg  = $('todos-trigger');
		todo.hide();

		if(opts.enable_todos)

			trg.addEvent('click', function() {
				todo.slideToggle('fast');
			});

		this.attachTodos();
		this.getTodos.bind(this).periodical(300000);

		if ($('duration')) this.b = this.getTimer.bind(this).periodical(60000);

	},

	updateState: function() {
		var obj = this;
		/*var fx  = this.state.effects({
			duration: 100,
			transition: Fx.Transitions.linear
		});*/

		new Ajax(this.url+'&task=updatestate', {
			method: 'post',
			data: 'description=' + obj.state.getValue(),
			onRequest: function(){
			//obj.state.addClass('loading');
			},
			onComplete: function(){
			//obj.state.removeClass('loading');
			/*fx.start({
					'background-color': '#ffffaa'
				}).chain(function(){
					this.setOptions({
						duration: 700
					});
					this.start({
						'background-color': '#ffffff'
					});
				});*/
			}
		}).request();
	},

	getTasks: function() {
		// set it again because project-id html element was reloaded by clicking on "todo"
		this.projectId = $('project-id');

		var id = this.projectId.getProperty('value');
		new Ajax(this.url+'&task=loadtasks', {
			method: 'post',
			//			data: 'project_id=' + id,
			data: 'project_id=' +  id + '&todo_id=0',
			update: $('task-id'),
			onComplete: function(){
				if(jQuery("#curtaskid")[0].selectedIndex > 0)
					load_task_description(jQuery("#curtaskid")[0]);
			}
		}).request();

		new Ajax(this.url+'&task=loaddescription', {
			method: 'post',
			data: 'project_id=' + id,
			update: $('project_data')
		}).request();

		jQuery("#task_data").html("");
		jQuery("#start_work").hide();
	},


	confirmDelete: function(e) {
		var event = new Event(e);
		if (!confirm(this.options.msgDeletelog)) event.stop();
	},

	getTeamLog: function() {
		var obj = this;
		//var bg  = $('yoo-teamlog').getElement('div.team-log-loading');

		new Ajax(this.url+'&task=loadteamlog', {
			method: 'get',
			update: $('team-log'),
			onRequest: function(){
			//bg.addClass('loading');
			},
			onComplete: function(){
				//bg.removeClass('loading');
				new Tips($$('div.team-log ul.log span.tooltip'));
			}
		}).request();
	},

	attachTodos: function() {
		var obj = this;

		$$('table.todo_table tr').addEvent('click', function(){

			var input = this.getElement('input');
			if (input == null) {
				return;
			}

			if(input.getAttribute("name") &&
				input.getAttribute("name").indexOf("filter_") >= 0) {
				return;
			}

			$('project-id').disabled = false;
			$('project-id').style.backgroundColor = "";
			$('curtaskid').disabled = false;
			$('curtaskid').style.backgroundColor = "";

			jQuery("#project-description").html("");
			//jQuery("#project_data").html("");
			jQuery("#task_data").html("");

			jQuery('table.todo_table input').each(function(i, n){
				if(n.type == 'hidden' && n.name.substr(0,4) == 'todo' && n != input){
					if(n.value == 1){
						n.value = 0;
						var o = jQuery(n).parent().children("div:first");
						o.attr("class", o.attr("class") == "chkbx"? "chkbx_checked" : "chkbx");
					}
				}
			});

			input.setProperty('value', (input.getProperty('value') == 1 ? 0 : 1));

			var current_todo = jQuery(this).find("div:first");
			if(input.getProperty('value') == 1){
				current_todo.attr("class", "chkbx_checked");
				jQuery("#start_work").show();
			}
			else{
				current_todo.attr("class", "chkbx");
				jQuery("#start_work").hide();
			}

			var chk = input.getProperty('value');
			var todo = $('todo_id');
			var pId = $('project-id');

			if(input.value == 0)
				todo.value = 0;

			obj.updateTodos();

			if(input.getProperty('value') == 1 && pId.disabled == true)
			{
				var todoId = input.getProperty('id');
				todoId = todoId.substr(5);

				// set hidden field value for todo of project/task form
				todo.setProperty('value', todoId);

				// show description of the todo
				new Ajax(obj.url+'&task=loadtodo', {
					method: 'post',
					data: 'todo_id=' + todoId,
					update: $('project-description')
				}).request();



			}
			if(input.getProperty('value') == 1 && pId.disabled == false)
			{
				var todoId = input.getProperty('id');
				todoId = todoId.substr(5);

				// set hidden field value for todo of project/task form
				todo.setProperty('value', todoId);

				// show description of the todo
				new Ajax(obj.url+'&task=loadtodo', {
					method: 'post',
					data: 'todo_id=' + todoId,
					update: $('project-description')
				}).request();

				// set project selectors when click on a todo
				new Ajax(obj.url+'&task=setproject', {
					method: 'post',
					data: 'todo_id=' + todoId,
					update: $('projectshape-id'),
					onRequest: function(){
					},
					onComplete: function(){
						var prj = $('project-id');
						var pv = prj.getProperty('value');

						// attach the event again because projectshape-id html element was reloaded
						prj.addEvent('change', obj.getTasks.bind(obj));

						new Ajax(obj.url+'&task=loadtasks', {
							method: 'post',
							data: 'project_id=' +  pv + '&todo_id=' + todoId,
							update: $('task-id'),
							onComplete: function(){
								jQuery("#project_data").load(
									"index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_project_description&todo_id="+todoId);
								jQuery("#task_data").load(
									"index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_task_description&todo_id="+todoId);

								$('project-id').style.backgroundColor="#FFDDDD";
								$('project-id').disabled = true;
								$('curtaskid').style.backgroundColor="#FFDDDD";
								$('curtaskid').disabled = true;
							}
						}).request();

					}
				}).request();

			}
		});

		// check current todo - for loading todo info
		var x = $$('table.todo_table tr div.chkbx_checked');
		if(x) {
			if(x.getParent()) {
				x = x.getParent();
			}
			if(x.getParent()) {
				x = x.getParent();
			}

			x.fireEvent('click');
			x.fireEvent('click');
		}

		// attach events for filter elements
		(function($) {

			$("#filter_stodo").keyup(function(e){
				if(e.which != 13){
					return;
				}

				$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_stodo",
				{
					filter_stodo: $("#filter_stodo").val()
				},
				function (data) {
					TeamlogObj.getTodos();
				});
			});

			$("#filter_sproject").keyup(function(e) {
				if(e.which != 13){
					return;
				}

				$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_sproject",
				{
					filter_sproject: $("#filter_sproject").val()
				},
				function (data) {
					TeamlogObj.getTodos();
				});
			});

		})(jQuery);
	},

	getTodos: function() {
		var obj = this;
		//var bg  = $('yoo-teamlog').getElement('div.todos-bg');

		new Ajax(this.url+'&task=loadtodos', {
			method: 'get',
			update: $('todos'),
			onRequest: function(){
			//bg.addClass('loading');
			},
			onComplete: function(){
				//bg.removeClass('loading');
				obj.attachTodos();

				jQuery("#yoo-teamlog div:first").css("height", null);

			//var s = "[" + obj.todos_str + jQuery("div.chkbx").length + "]";
			//jQuery("#todos-trigger").html(s);
			}
		}).request();
	},


	getTimer: function() {
		//check if pause set
		var x = $('bPause');
		if(x.disabled){
			//alert("paused");
			return;
		}
		else
			//alert("time");


			var a = this;
		new Ajax(this.url+'&task=loadtimer', {
			method: 'post',
			update: $('duration'),
			// if timer was set manually then - stop auto refresh the timer
			onComplete: function(){
				var b = a.b;
				this.hoursId = $('hours');
				this.minutesId = $('minutes');
				this.hoursId.addEvent('change', function() {
					b = $clear(b);
				});
				this.minutesId.addEvent('change', function() {
					b = $clear(b);
				});

			//set client time
			/*var v = parseInt(jQuery("#hours").val()) +
					new Date().getTimezoneOffset()/60;
				if(v < 0)
					v = 0;
				jQuery("#hours").val(v);*/
			}
		}).request();
	},

	startLog: function() {
	/*		var id = this.projectId.getProperty('value');
		var task = this.taskId.getProperty('value');
		var task = this.taskId.getProperty('value');
		var pars = {'project_id': id, 'task_id': task, 'type_id': type};
		new Ajax(this.url+'&task=startlog', {
			method: 'post',
			parameters: pars,
			update: $('project-description')
		}).request();
     */
	},



	updateTodos: function() {
		var obj = this;
		/*var bg  = $('yoo-teamlog').getElement('table.todo_table');
		var fx  = bg.effects({
			duration: 100,
			transition: Fx.Transitions.linear
		});*/

		$clear(this.timeout);
		this.timeout = function() {
			$('todos-form').send({
				onRequest: function(){
				/*bg.addClass('loading');*/
				},
				onComplete: function(){
				/*bg.removeClass('loading');
					fx.start({
						'background-color': '#FFE678'
					}).chain(function(){
						this.setOptions({
							duration: 700
						});
						this.start({
							'background-color': '#ffffaa'
						});
					});*/
				}
			});
		}.delay(1000);
	}
});

Teamlog.implement(new Options);

var Observer = new Class({

	options: {
		'periodical': false,
		'delay': 1000
	},

	initialize: function(el, onFired, options){
		this.setOptions(options);
		this.addEvent('onFired', onFired);
		this.element = $(el);
		this.listener = this.fired.bind(this);
		this.value = this.element.getValue();
		if (this.options.periodical) this.timer = this.listener.periodical(this.options.periodical);
		else this.element.addEvent('keyup', this.listener);
	},

	fired: function() {
		var value = this.element.getValue();
		if (this.value == value) return;
		this.clear();
		this.value = value;
		this.timeout = this.fireEvent.delay(this.options.delay, this, ['onFired', [value]]);
	},

	clear: function() {
		$clear(this.timeout);
		return this;
	}
});

Observer.implement(new Options);
Observer.implement(new Events);

//---

//function set_todo_id(id){
//jQuery('#todo_id').val(id);
//jQuery('#project-id').attr("disabled", true);
//jQuery('#curtaskid').attr("disabled", true);
//}

var log_form_onsubmit = function () {
	$('project-id').disabled = false;
	$('curtaskid').disabled = false;

	return true;
};

var load_task_description = function (obj) {
	var $ = jQuery;

	$("#start_work").show();
	$.get("index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_task_description&task_id=" +
		obj.value,
		function (data) {
			$("#task_data").html(data);
		});
};

var set_filter_state = function () {
	var $ = jQuery;

	$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_state",
	{
		filter_state: $("#filter_state").val()
	},
	function (data) {
		TeamlogObj.getTodos();
	});
};

var set_filter_period = function () {
	var $ = jQuery;

	$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_period",
	{
		filter_period: $("#filter_period").val()
	},
	function (data) {
		TeamlogObj.getTodos();
	});
};

var set_filter_date = function() {
	var $ = jQuery;

	$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_date",
	{
		filter_date: $("#filter_date").val()
	},
	function (data) {
		TeamlogObj.getTodos();
	});
};

jQuery(function ($) {

	// init highslide
	hs.graphicsDir = TeamTime.baseUrl + "components/com_teamtime/assets/highslide/graphics/";
	hs.outlineType = "rounded-white";
	hs.wrapperClassName = "draggable-header";
	hs.showCredits = false;
	hs.width = 740;

	// init "close todo" checkbox
	$('#toggle_close_todo').click(
		function () {
			if ($('#close_todo').attr("checked")) {
				$('#close_todo').removeAttr("checked");
			}
			else {
				$('#close_todo').attr("checked", "checked");
			}

			return false;
		});

});
