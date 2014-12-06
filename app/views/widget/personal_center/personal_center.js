define(['personal/port', 'jquery', 'jquery.uploadify'], function(port, $) {
	console.log("personal center loaded");
	$(".recent_order").on('click', function(){
		$(".tab_header li").each(function(index, value){
			$(value).removeClass("active");
		});
		$(this).addClass("active");
		$('.recent_ticket').show();
		$(".recent_month").hide();
	});

	$(".recent_deal").on('click', function(){
		$(".tab_header li").each(function(index, value){
			$(value).removeClass("active");
		});
		$(this).addClass("active");
		$(".recent_ticket").hide();
		$(".recent_month").show();
	});

	$(".avatar").on('click', function(e){
		$("#upload_btn")[0].click();
	});

	$("#upload_btn").uploadify({
		'swf': '/js/lib/uploadify.swf',                        //FLash文件路径
		'uploader': port['imageUpload'], //处理ASHX页面
		'formData' : { },         //传参数
		'width'    : '48',
		'height'   : "42",
		'buttonText': '上传头像',
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


});