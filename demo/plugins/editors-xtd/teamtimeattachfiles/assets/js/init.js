var plgButtonTeamtimeAttachFiles = {

	jQuery: jQuery.noConflict(),

	baseUrl: "",
	rootUrl: "",

	getUrlData: function (task) {
		if (typeof task == "undefined") {
			task = "upload";
		}

		var type = "";
		var id = "";
		if (this.jQuery(".main-editor").length == 1) {
			type = this.jQuery(".main-editor").attr("data-field-type");
			id = this.jQuery(".main-editor").attr("data-current-id");
		}

		var result = "";
		//var result = {};
		//result.script = this.baseUrl + "assets/js/libs/jquery/uploadify/ajax.php";
		result = this.rootUrl + "index.php?option=com_teamtimeattachments"
		+ "&controller=attachments&task=" + task;

		//console.log(this.sessionName);
		//console.log(this.sessionId);

		//var params = {};
		//params.task = task;
		//params.controller = "attachments";
		//params.option = "com_teamtimeattachments";

		//params["_sn"] = this.sessionName;
		//params["_si"] = this.sessionId;
		
		if (type != "") {
			//params.type = type;
			result += "&type=" + type;
		}
		if (id != "") {
			//params.id = id;
			result += "&id=" + id;
		}

		//result.params = params;

		return result;
	},
	
	fileUploadCallback: function (data, editorName) {
		var $ = jQuery;
			
		if ("errors" in data) {
			alert(data.errors);
			$.fancybox.close();
			return;
		}
			
		var text = $('#plgButtonTeamtimeAttachFiles .filename').val();
			
		if (text === '')
		{
			text = data[0].filename;
		}
			
		var linkBuff = [this.lang.label_loaded_results];			
		if (data.length == 1) {
			linkBuff.push('<a href="' + data[0].filelink + '">' + text + '</a>');
		}
		else {
			var l = data.length;
			for (var i = 0; i < l; i++) {
				linkBuff.push('<a href="' + data[i].filelink + '">' + data[i].filename + '</a><br>');
			}
		}
			
		var link = linkBuff.join("");
		
		//console.log("insertcode:\n" + link);
		if (typeof jInsertEditorText == "function") {
			jInsertEditorText(link, editorName);
		}
			
		$.fancybox.close();
	},

	uploadDialog: function (name) {
		var $ = jQuery;
		var self = this;

		$.fancybox({
			href: this.baseUrl + "upload.html",
			type: 'ajax',
			width: 400,
			height: 450,
			autoSize: false,
			padding : 1,

			afterShow: function () {
				var data = self.getUrlData("upload");
				//console.log(data);

				$("#plgButtonTeamtimeAttachFiles").ajaxForm({
					url: data,
					dataType: 'json',					
					success: function (data) {
						self.fileUploadCallback.call(self, data, name);
					}
				});

			/*
				$("#plgButtonTeamtimeAttachFiles-files").uploadify({
					height: 30,
					width: 120,
					swf: self.baseUrl + 'assets/js/libs/jquery/uploadify/uploadify.swf',

					uploader: data.script,
					formData: data.params,

					onUploadError: function (file, errorCode, errorMsg, errorString) {
						alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
					}
				});
				*/
			}
		});

	}

};

plgButtonTeamtimeAttachFiles.jQuery(function ($) {
	/*
	var plugin = plgButtonTeamtimeAttachFiles;

	console.log("test: " + plugin.getUrlForTask());

//...*/

	});