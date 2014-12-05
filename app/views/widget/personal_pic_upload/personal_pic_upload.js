define(['port', 'jquery', 'jquery-ui', 'jquery.Jcrop', 'jquery.uploadify'], function(port){
	console.log("personal pic loaded");
	$(".shadow").css({
		visibility : "visible"
	})
	// Create variables (in this scope) to hold the API and image size
	var jcrop_api,
		boundx,
		boundy,

	// Grab some information about the preview pane
		$preview = $('#preview-pane'),
		$pcnt = $('#preview-pane .preview-container'),
		$pimg = $('#preview-pane .preview-container img'),

		xsize = $pcnt.width(),
		ysize = $pcnt.height();

	$('#target').Jcrop({
		onChange: updatePreview,
		onSelect: updatePreview,
		aspectRatio: xsize / ysize,
		boxWidth : 250,
		boxHeight: 140
	},function(){
		// Use the API to get the real image size
		var bounds = this.getBounds();
		boundx = bounds[0];
		boundy = bounds[1];
		// Store the API in the jcrop_api variable
		jcrop_api = this;

		// Move the preview into the jcrop container for css positioning
		$preview.appendTo(jcrop_api.ui.holder);
	});

	function updatePreview(c)
	{
		if (parseInt(c.w) > 0)
		{
			var rx = xsize / c.w;
			var ry = ysize / c.h;

			$pimg.css({
				width: Math.round(rx * boundx) + 'px',
				height: Math.round(ry * boundy) + 'px',
				marginLeft: '-' + Math.round(rx * c.x) + 'px',
				marginTop: '-' + Math.round(ry * c.y) + 'px'
			});
		}
		$('#x1').val(c.x);
		$('#y1').val(c.y);
		$('#x2').val(c.x2);
		$('#y2').val(c.y2);
		$('#width').val(c.w);
		$('#height').val(c.h);
	};

	$("#upload_btn").uploadify({
		'swf': '/js/lib/uploadify.swf',                        //FLash文件路径
		'uploader': port['imageUpload'], //处理ASHX页面
		'formData' : { },         //传参数
		'width'    : '40',
		'height'   : "20",
		'buttonText': '上传',
		'queueID': 'fileQueue',                        //队列的ID
		'queueSizeLimit': 10,                           //队列最多可上传文件数量，默认为999
		'auto': true,                                 //选择文件后是否自动上传，默认为true
		'multi': true,                                 //是否为多选，默认为true
		'removeCompleted': true,                       //是否完成后移除序列，默认为true
		'fileSizeLimit': '10MB',                       //单个文件大小，0为无限制，可接受KB,MB,GB等单位的字符串值
		'fileTypeDesc': 'person image',                 //文件描述
		'fileTypeExts': '*.gif; *.jpg; *.png; *.bmp',  //上传的文件后缀过滤器
		'onQueueComplete': function (event, data) {    //所有队列完成后事件
			//ShowUpFiles(guid, type, show_div);

			var nextSrc = data['nextSrc'];
			window.location.href = nextSrc;
		},
		'onUploadError': function (event, queueId, fileObj, errorObj) {
			alert(errorObj.type + "：" + errorObj.info);
		}
	});



	var cuterHeight = $(".jcrop-holder").height();

	if(cuterHeight < 250){
		$(".jcrop-holder").css({
			"top" : (250 - cuterHeight) / 2
		});
		$("#preview-pane").css({
			"top" : - ((250 - cuterHeight) / 4)
		})
	}


});

