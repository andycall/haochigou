JSON=new function(){this.decode=function(){var a,b,c,d;if($$("toString")){switch(arguments.length){case 2:c=arguments[0],a=arguments[1];break;case 1:$[typeof arguments[0]](arguments[0])===Function?(c=this,a=arguments[0]):c=arguments[0];break;default:c=this}if(!rc.test(c))throw new JSONError("bad data");try{if(b=e("(".concat(c,")")),a&&null!==b&&(d=$[typeof b](b))&&(d===Array||d===Object))for(c in b)b[c]=v(c,b)?a(c,b[c]):b[c]}catch(f){}}return b},this.encode=function(){var a,b,c=arguments.length?arguments[0]:this;if(null===c)a="null";else if(void 0!==c&&(b=$[typeof c](c)))switch(b){case Array:a=[];for(var e=0,f=0,g=c.length;g>f;f++)void 0!==c[f]&&(b=JSON.encode(c[f]))&&(a[e++]=b);a="[".concat(a.join(","),"]");break;case Boolean:a=String(c);break;case Date:a='"'.concat(c.getFullYear(),"-",d(c.getMonth()+1),"-",d(c.getDate()),"T",d(c.getHours()),":",d(c.getMinutes()),":",d(c.getSeconds()),'"');break;case Function:break;case Number:a=isFinite(c)?String(c):"null";break;case String:a='"'.concat(c.replace(rs,s).replace(ru,u),'"');break;default:var h,e=0;a=[];for(h in c)void 0!==c[h]&&(b=JSON.encode(c[h]))&&(a[e++]='"'.concat(h.replace(rs,s).replace(ru,u),'":',b));a="{".concat(a.join(","),"}")}return a},this.toDate=function(){var a,b=arguments.length?arguments[0]:this;return rd.test(b)?(a=new Date,a.setHours(i(b,11,2)),a.setMinutes(i(b,14,2)),a.setSeconds(i(b,17,2)),a.setMonth(i(b,5,2)-1),a.setDate(i(b,8,2)),a.setFullYear(i(b,0,4))):rt.test(b)&&(a=new Date(1e3*b)),a};var c={"\b":"b","	":"t","\n":"n","\f":"f","\r":"r",'"':'"',"\\":"\\","/":"/"},d=function(a){return 10>a?"0".concat(a):a},e=function(c,f,e){return e=eval,delete eval,"undefined"==typeof eval&&(eval=e),f=eval(""+c),eval=e,f},i=function(a,b,c){return 1*a.substr(b,c)},p=["","000","00","0",""],rc=null,rd=/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/,rs=/(\x5c|\x2F|\x22|[\x0c-\x0d]|[\x08-\x0a])/g,rt=/^([0-9]+|[0-9]+[,\.][0-9]{1,3})$/,ru=/([\x00-\x07]|\x0b|[\x0e-\x1f])/g,s=function(a,b){return"\\".concat(c[b])},u=function(a,b){var c=b.charCodeAt(0).toString(16);return"\\u".concat(p[c.length],c)},v=function(a,b){return $[typeof result](result)!==Function&&(b.hasOwnProperty?b.hasOwnProperty(a):b.constructor.prototype[a]!==b[a])},$={"boolean":function(){return Boolean},"function":function(){return Function},number:function(){return Number},object:function(a){return a instanceof a.constructor?a.constructor:null},string:function(){return String},undefined:function(){return null}},$$=function(a){function b(b,c){c=b[a],delete b[a];try{e(b)}catch(d){return b[a]=c,1}}return b(Array)&&b(Object)};try{rc=new RegExp('^("(\\\\.|[^"\\\\\\n\\r])*?"|[,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t])+?$')}catch(z){rc=/^(true|false|null|\[.*\]|\{.*\}|".*"|\d+|\d+\.\d+)$/}};