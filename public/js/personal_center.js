define(["personal/port","jquery","jquery.uploadify"],function(a,b){console.log("personal center loaded"),b(".recent_order").on("click",function(){b(".tab_header li").each(function(a,c){b(c).removeClass("active")}),b(this).addClass("active"),b(".recent_ticket").show(),b(".recent_month").hide()}),b(".recent_deal").on("click",function(){b(".tab_header li").each(function(a,c){b(c).removeClass("active")}),b(this).addClass("active"),b(".recent_ticket").hide(),b(".recent_month").show()}),b(".avatar").on("click",function(){b("#upload_btn")[0].click()}),b("#user_name").on("blur",function(){var c=b(this).text();console.log(c),b.ajax({url:a.change_user_name,type:"POST",data:JSON.stringify({user_name:c}),contentType:"application/json; charset=utf-8",dataType:"json",async:!1}).fail(function(a){alert(a.errormsg)})}),b("#upload_btn").uploadify({swf:"/js/lib/uploadify.swf",uploader:a.imageUpload,formData:{},width:"48",height:"42",buttonText:"上传头像",queueID:"fileQueue",queueSizeLimit:10,auto:!0,multi:!0,removeCompleted:!0,fileSizeLimit:"10MB",fileTypeDesc:"person image",fileTypeExts:"*.gif; *.jpg; *.png; *.bmp",onQueueComplete:function(a,b){var c=b.nextSrc;window.location.href=c},onUploadError:function(a,b,c,d){alert(d.type+"："+d.info)}})});
