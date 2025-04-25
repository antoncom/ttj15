
TeamTime.form.Log = {
	obj: null,

	init: function () {
		var $ = TeamTime.jQuery;

		$("#mainFormLog").submit(function () {
			$('#project-id').removeAttr("disabled");
			$('#curtaskid').removeAttr("disabled");

			if (TeamTime.resource.todos.currentLog) {
				/*
				var logs = [];
				$('#project-description li').each(function (i, n) {
					var v;
					var checked = $(n).children("div.checklist-checkbox").hasClass("checked");
					if (checked) {
						logs.push(i);
					}
				});*/
				$("#logs_checklist").val($("#project-description div.todo-description").html());
			}

			return true;
		});
	},

	initTodosFilter: function () {
		var $ = TeamTime.jQuery;
		var self = this;

		$("#filter_stodo").keyup(function (e) {
			if (e.which != 13) {
				return;
			}
			$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_stodo", {
				filter_stodo: $("#filter_stodo").val()
			},
			function (data) {
				self.obj.getTodos();
			});
		});

		$("#filter_sproject").keyup(function (e) {
			if (e.which != 13) {
				return;
			}

			$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_sproject", {
				filter_sproject: $("#filter_sproject").val()
			},
			function (data) {
				self.obj.getTodos();
			});
		});
	},

	addChecklistOption: function (i, html, $el) {
		var $ = TeamTime.jQuery;
		var $obj = $('div.main-editor .redactorEditor');
		if ($obj.length == 0) {
			return;
		}
		var editor = $obj.getEditor();
		var id = "checklist-option" + i;

		if ($("div." + id, editor).length == 0) {
			$(editor).append('<div class="' + id + '">' + 
				TeamTime.resource.todos.text.done + html + '</div>');
			$('div.checklist-checkbox', editor).remove();
		}
		else {
			$("div." + id, editor).html(html);
		}
		$obj.getObject().syncCode();
	
		$el.toggleClass("checked");
	},

	removeChecklistOption: function (i, $el) {
		var $ = TeamTime.jQuery;
		var $obj = $('div.main-editor .redactorEditor');
		if ($obj.length == 0) {
			return;
		}
		var editor = $obj.getEditor();
		var id = "checklist-option" + i;
		
		if (confirm(TeamTime.resource.todos.text.confirm_remove_option)) {
			$("div." + id, editor).remove();
			$obj.getObject().syncCode();
			
			$el.toggleClass("checked");
		}
	},

	initTodoChecklist: function (currentTodoId) {
		var $ = TeamTime.jQuery;
		var self = this;

		//$.getJSON(TeamTime.getUrlForController("api"), {
		//	task: "todo_update",
		//	id: currentTodoId,
		//},
		//function (data) {

		$('#project-description li').each(function (i, n) {
			if ($(n).hasClass("checklist-hoursplan") || $(n).hasClass("checklist-vector")) {
				//if (_.indexOf(data.logs_checklist, i)) {
				if ($(n).children("div.checklist-checkbox").length == 0) {
					//$(n).prepend('<div class="checklist-checkbox checked done"></div>');
					$(n).prepend('<div class="checklist-checkbox"></div>');
				}
			//else {
			//	$(n).prepend('<div class="checklist-checkbox"></div>');
			//}
			}
		});

		if (TeamTime.resource.todos.currentLog) {
			$(".checklist-checkbox").each(function (i, n) {
				$(n).click(function () {
					var $el = $(this);
					if ($el.hasClass("done")) {
						return;
					}

					var $li = $el.closest("li");					
					if (!$el.hasClass("checked")) {
						self.addChecklistOption(i, $li.html(), $el);
					}
					else {
						self.removeChecklistOption(i, $el);
					}
				//$el.addClass("done");
				});
			});
		}

	//});
	}
}

var Teamlog = new Class({
	initialize: function (url, options) {
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
		var j$ = TeamTime.jQuery;
		new Tips($$('ul.log span.tooltip'));

		// logs
		new Observer(this.state, this.updateState.bind(this), {
			delay: 1000
		});
		this.projectId.addEvent('change', this.getTasks.bind(this));
		this.logDelete.addEvent('click', this.confirmDelete.bind(this));

		if ($('team-log')) {
			this.getTeamLog.bind(this).periodical(350000);
		}

		// todos
		var todo = j$("#yoo-teamlog div.todos")
		var trg  = $('todos-trigger');
		todo.hide();

		if (opts.enable_todos) {
			trg.addEvent('click', function() {
				todo.slideToggle('fast');
			});
		}

		this.attachTodos();
		this.getTodos.bind(this).periodical(300000);

		if ($('duration')) {
			this.b = this.getTimer.bind(this).periodical(60000);
		}
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

	getTasks: function () {
		var j$ = TeamTime.jQuery;

		// set it again because project-id html element was reloaded by clicking on "todo"
		this.projectId = $('project-id');

		var id = this.projectId.getProperty('value');
		new Ajax(this.url+'&task=loadtasks', {
			method: 'post',
			//			data: 'project_id=' + id,
			data: 'project_id=' +  id + '&todo_id=0',
			update: $('task-id'),
			onComplete: function () {
				if (j$("#curtaskid")[0].selectedIndex > 0) {
					load_task_description(j$("#curtaskid")[0]);
				}
			}
		}).request();

		new Ajax(this.url+'&task=loaddescription', {
			method: 'post',
			data: 'project_id=' + id,
			update: $('project_data')
		}).request();

		j$("#task_data").html("");
		j$("#start_work").hide();
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
		var j$ = TeamTime.jQuery;
		var obj = this;

		$$('table.todo_table tr').addEvent('click', function () {
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

			j$("#project-description").html("");
			//j$("#project_data").html("");
			j$("#task_data").html("");

			j$('table.todo_table input').each(function (i, n) {
				if (n.type == 'hidden' && n.name.substr(0,4) == 'todo' && n != input) {
					if (n.value == 1) {
						n.value = 0;
						var o = j$(n).parent().children("div:first");
						o.attr("class", o.attr("class") == "chkbx"? "chkbx_checked" : "chkbx");
					}
				}
			});

			input.setProperty('value', (input.getProperty('value') == 1 ? 0 : 1));

			var current_todo = j$(this).find("div:first");
			if (input.getProperty('value') == 1) {
				current_todo.attr("class", "chkbx_checked");
				j$("#start_work").show();
			}
			else {
				current_todo.attr("class", "chkbx");
				j$("#start_work").hide();
			}

			var chk = input.getProperty('value');
			var todo = $('todo_id');
			var pId = $('project-id');

			if (input.value == 0) {
				todo.value = 0;
			}
			obj.updateTodos();

			if (input.getProperty('value') == 1 && pId.disabled == true) {
				var todoId = input.getProperty('id');
				todoId = todoId.substr(5);

				// set hidden field value for todo of project/task form
				todo.setProperty('value', todoId);

				// show description of the todo
				new Ajax(obj.url + '&task=loadtodo', {
					method: 'post',
					data: 'todo_id=' + todoId,
					update: $('project-description')
				}).request();
			}

			if (input.getProperty('value') == 1 && pId.disabled == false) {
				var todoId = input.getProperty('id');
				todoId = todoId.substr(5);

				// set hidden field value for todo of project/task form
				todo.setProperty('value', todoId);

				j$.get(obj.url + '&task=loadtodo', {
					'todo_id': todoId
				},
				function (data) {
					j$('#project-description').html(data);
					TeamTime.form.Log.initTodoChecklist(todoId);
				});

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
							onComplete: function () {
								j$("#project_data").load(
									"index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_project_description&todo_id="+todoId);
								j$("#task_data").load(
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
		if (x) {
			if (x.getParent()) {
				x = x.getParent();
			}
			if (x.getParent()) {
				x = x.getParent();
			}
			x.fireEvent('click');
			x.fireEvent('click');
		}

		TeamTime.form.Log.initTodosFilter();
	},

	getTodos: function() {
		var j$ = TeamTime.jQuery;
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

				j$("#yoo-teamlog div:first").css("height", null);

			//var s = "[" + obj.todos_str + j$("div.chkbx").length + "]";
			//j$("#todos-trigger").html(s);
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
			/*var v = parseInt(j$("#hours").val()) +
					new Date().getTimezoneOffset()/60;
				if(v < 0)
					v = 0;
				j$("#hours").val(v);*/
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

// ---

//function set_todo_id(id){
//var $ = TeamTime.jQuery;
//$('#todo_id').val(id);
//$('#project-id').attr("disabled", true);
//$('#curtaskid').attr("disabled", true);
//}

var load_task_description = function (obj) {
	var $ = TeamTime.jQuery;

	$("#start_work").show();
	$.get("index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_task_description&task_id=" +
		obj.value,
		function (data) {
			$("#task_data").html(data);
		});
};

var set_filter_state = function () {
	var $ = TeamTime.jQuery;

	$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_state",
	{
		filter_state: $("#filter_state").val()
	},
	function (data) {
		TeamTime.form.Log.obj.getTodos();
	});
};

var set_filter_period = function () {
	var $ = TeamTime.jQuery;

	$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_period",
	{
		filter_period: $("#filter_period").val()
	},
	function (data) {
		TeamTime.form.Log.obj.getTodos();
	});
};

var set_filter_date = function() {
	var $ = TeamTime.jQuery;

	$.post("index.php?option=com_teamtime&controller=&view=log&format=raw&task=set_filter_date",
	{
		filter_date: $("#filter_date").val()
	},
	function (data) {
		TeamTime.form.Log.obj.getTodos();
	});
};

TeamTime.jQuery(function ($) {
	// init highslide
	hs.graphicsDir = TeamTime.baseUrl + "components/com_teamtime/assets/highslide/graphics/";
	hs.outlineType = "rounded-white";
	hs.wrapperClassName = "draggable-header";
	hs.showCredits = false;
	hs.width = 740;

	// init "close todo" checkbox
	$('#toggle_close_todo').click(function () {
		if ($('#close_todo').attr("checked")) {
			$('#close_todo').removeAttr("checked");
		}
		else {
			$('#close_todo').attr("checked", "checked");
		}

		return false;
	});

});

TeamTime.jQuery(function ($) {
	// init todos
	var todosVars = TeamTime.resource.todos;

	var app = new Teamlog(todosVars.removeLogUrl, {
		msgDeletelog: todosVars.text.are_you_sure_to_delete_this_log
	});
	TeamTime.form.Log.obj = app;
	TeamTime.form.Log.obj.todos_str = todosVars.text.todos_str;

	if (todosVars.currentLog) {
		app.attachEvents({
			enable_todos:false
		});
	}
	else {
		app.attachEvents({
			enable_todos:true
		});
	}

	// set client time
	var set_local_time = function(){
		return;

		var v = parseInt($("#hours").val()) +
		new Date().getTimezoneOffset()/60;
		if(v < 0)
			v = 0;
		$("#hours").val(v);
	};

	var pauseClick = function () {
		$.post("/index.php?option=com_teamtime&task=set_pause",
			function(data){
				set_local_time();

				$('#bPause').attr("disabled", "disabled");
				$('#bStop').css({
					"display": "none"
				});
				$('#bContinue').css({
					"display": ""
				});
				$('#imagePause').css({
					"display": ""
				});

				$('#hours').attr("disabled", "disabled");
				$('#minutes').attr("disabled", "disabled");
			});
	};
	$('#bPause').click(pauseClick);

	$('#bContinue').click(function () {
		$.post("/index.php?option=com_teamtime&task=reset_pause",
			function(data){
				$('#bPause').removeAttr("disabled");
				$('#bStop').css({
					"display": ""
				});
				$('#bContinue').css({
					"display": "none"
				});
				$('#imagePause').css({
					"display": "none"
				});

				$('#hours').removeAttr("disabled");
				$('#minutes').removeAttr("disabled");
			});
	});

	/*$.get("/index.php?option=com_teamtime&task=check_pause&t="+new Date().getTime(),
    function(data){
      //if not begin - no check pause
      if($("#bStart").length > 0) return;
      if(parseInt(data) != 0){
        $.post("/index.php?option=com_teamtime&task=reset_pause&t="+new Date().getTime(),
          function(data){
            pauseClick();
          });
      }
      else
        set_local_time();
    });*/

	if ($("#bStart").length == 0) {
		$('#tmoney').autoNumeric({
			mNum:5,
			mDec:2,
			aSep:''
		});
		$('#money').css({
			"display": ""
		});
	}

	$('#start_work').hide();

	if (todosVars.currentLog) {
		var project_id = todosVars.currentLog.project_id;
		var task_id = todosVars.currentLog.task_id;

		$("#project_data").load(
			"index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_project_description&project_id="+
			project_id);

		$("#task_data").load(
			"index.php?option=com_teamtime&controller=&view=log&format=raw&task=load_task_description&task_id="+
			task_id);

		$("#todos-trigger").removeClass('todos-trigger').addClass('todos-trigger1');
		$("#projects-trigger").removeClass('todos-trigger').addClass('todos-trigger1');
	}
	else {
		$("#projectshape-id").hide();
		$("#task-id").hide();

		$('#projects-trigger').click(function() {
			$("#projectshape-id").toggle();
			$("#task-id").toggle();
		});
	}

	TeamTime.form.Log.init();
});
