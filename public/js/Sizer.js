define(["jquery"],function(a){function b(){this.data=[]}return b.prototype.add=function(b){var c=this;if(!a.isFunction(b))for(var d=0,e=b.length;e>d;d++)c.data.push(b[d])},b.prototype.get=function(b){var c=this;if(c.data,a.isPlainObject(b)){for(var d=[],e=0,f=c.data.length;f>e;e++){var g=c.data[e],h=!0;for(var i in b){if("support_activity"==i)for(var j=b[i],k=0,l=j.length;l>k;k++)g.support_activity[k]!=j[k]&&(h=!1);"support_activity"!=i&&g[i]!=b[i]&&(h=!1)}h&&d.push(g)}return d}},new b});