define(["jquery","order/port"],function(a,b){function c(){var b=a(".js-adress-modify"),c=b.find(".tel"),d=b.find(".name"),e=b.find(".addr"),f=b.find(".bk");b.find("input").on("focus",function(){a(this).css("border-color","#bbb")});var g=!0,i=/^[\d]{11}$/,j=/^[\d]{6,15}/,k=/^[\S\s]+$/i;return k.test(d.val())?(d.css("border-color","#bbb"),h.find(".user-name").val(d.val()),m.name=d.val()):(d.css("border-color","red"),g=!1),i.test(c.val())?(c.css("border-color","#bbb"),m.phone=c.val(),h.find(".user-tel").val(c.val())):(c.css("border-color","red"),g=!1),k.test(e.val())?(e.css("border-color","#bbb"),h.find(".user-addr").val(e.val()),m.addr=e.val()):(e.css("border-color","red"),g=!1),j.test(f.val())&&(h.find(".user-bkTel").val(f.val()),m.bkTel=f.val()),g?(a(".u-mask").hide(),a(".js-cmodal-wrapper").hide(),!0):!1}function d(a){e(b.orderAuth,{type:"sms",phone:m.phone,csrf_token:m.csrf_token},a)}function e(b,c,d){a.post(b,c,function(b){if("object"!=typeof b)try{b=a.parseJSON(b)}catch(c){return void alert("服务器数据错误！！！")}"true"==b.success?d.sccuess(b):d.failed(b)})}var f=a(".js-sms-auth-wrapper"),g=a(".u-mask"),h=a(".js-save-bottom");a(".js-open-edit").on("click",function(){a(".u-mask").show(),a(".js-cmodal-wrapper").show()}),a(".js-exit-edit").on("click",function(){a(".u-mask").hide(),a(".js-cmodal-wrapper").hide()});var i=a(".js-select-time"),j=i.find(".ctime-toggle"),k=i.find(".ctime-dropdown"),l=i.find(".ctime-item");j.on("click",function(a){a.stopPropagation(),k.addClass("on")}),l.on("click",function(){var b=a(this).text();j.text(b),h.find(".order-time").val(b)}),a(document.body).on("click",function(){k.removeClass("on")}),a(".cpayment-choice").on("click",function(){var b=a(this);b.hasClass("ui_disabled")||b.hasClass("ui_selected")||(a(".cpayment-choice").removeClass("ui_selected"),b.addClass("ui_selected"),h.find(".order-way").val(b.attr("data-pay-way")))}),a(".js-exit-auth").on("click",function(){f.hide(),g.hide()});var m={csrf_token:h.find('input[name="_token"]').val()};a(".js-save-edit").on("click",function(b){c()?(a(".js-show-addr-info").find(".current_addr").text(m.addr).end().find(".current_name").text(m.name).end().find(".current_tel").text(m.phone).end().find(".current_bkTel").text(m.bkTel),a(".u-mask").hide$(".js-cmodal-wrapper").hide()):b.preventDefault()}),h.on("submit",function(b){return b.preventDefault(),c()?""==h.find(".order-time").val()?(i.find(".ui-err-notice").show(),!1):(i.find(".ui-err-notice").hide(),void d({success:function(a){f.show(),g.show(),h.find(".user-auth").val(a.auth)},failed:function(a){a.errMsg?(f.hide(),g.hide(),alert(a.Msg)):alert("验证码发送失败, 请重试！！")}})):(a(".js-cmodal-wrapper").show(),a(".u-mask").show(),!1)}),a(".js-repeat-send-auth").on("click",function(){var b=a(this);d({success:function(a){h.find(".user-auth").val(a.auth),b.attr("disabled","disabled");var c=60,d=setInterval(function(){b.text(c--+"秒后可重新发送"),0>c&&(clearInterval(d),b.text("重新发送"),b.removeAttribute("disabled"))},1e3)},failed:function(a){alert(a.errMsg?a.Msg:"验证码发送失败, 请重试！！")}})}),a(".js-send-confirm-auth").on("click",function(){e(b.confirmAuth,{auth:a(".js-confirm-auth").val(),csrf_token:m.csrf_token},{success:function(a){alert(a.successMsg?a.successMsg:"验证码正确，稍后会为您送来!!!"),f.hide(),g.hide(),h[0].submit()},failed:function(a){alert(a.errMsg?errMsg:"验证码错误!!!!,请重填!!")}})})});