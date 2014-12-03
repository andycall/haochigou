/**
 * @desc grunt配置文件
 * @author zhaoyang
 * @date 2014-11-1
 * @new 避免每次操作全局压缩，目前只对修改的静态文件进行压缩操作
 */

module.exports = function(grunt) {
  var path = require("path"),
      sep = path.sep;

  grunt.initConfig({
    //读取配置文件信息
  	config: grunt.file.readJSON('grunt.config.json'),

    //文件修改监控
    watch : {
        options: {
            spawn: false
        },
        files: ['<%= config.js %>', '<%= config.css %>', '<%= config.image %>']
    },

	imagemin : {
		static: {
			files: [{
				expand: true,
				filter: 'isFile',
				src: ["app/views/**/*.{jpg,png,gif}"],
				dest: "public/images/",
				rename: function(dest,src){
					var lIndex = src.lastIndexOf("/");
					lIndex = lIndex == -1 ? 0 : lIndex+1;

					return dest + src.slice(lIndex);
				}
			}]
		}
	}


  });

  //对watch插件的watch事件进行监听，进行针对处理
  grunt.event.on("watch",function(type,src){
      var data = grunt.config.data,
          depth = data.config.depth || 1,
          needTag = data.config.needTag || false,
          imgDir = getDirPathFromSrc(src),
          ext = getExtFromSrc(src),
          isJ = ["js","json"].indexOf(ext) != -1,
          isC = "css" === ext,
          isJ_M ="js.map" === ext,
          isC_M = "css.map" === ext,
		  isScss = "scss" === ext,
		  isLess = "less" === ext,
          runMap = [];



      if((isJ || isC) & needTag)
      {
        data.clean = {
          build: {
            expand: true,
            filter: "isFile",
            src: getCleanDir(src,depth)
          }
        };

        runMap.push("clean");
      }


      if(isJ)
      {
        data.jshint = {
            options: {
              globals: {
                jQuery: true,
                console: true,
                module: true
              }
            },
            files: [src]
          };

        data.uglify = {
            compress: {
	            options : {
		            preserveComments : "all",
		            mangle: false,
		            beautify: true
	            },
                files: [
                {
	                expand: true,
	                filter: 'isFile',
	                src: src,
	                rename: getPathFromDepth("public/js", src, depth,needTag)
	              }
            ]
          },
          //beatify: {
          //  options: {
          //    beautify: true,
          //    mangle: false
          //  },
          //  files: [
          //    {
          //      expand: true,
          //      filter: 'isFile',
          //      src: src
          //    }]
          //}
        };

//        runMap.push("jshint");
        runMap.push("uglify");
      }

	  if(isJ_M){
		  data.copy = {
			  main : {
				  files : [
					  {
						  expand : true,
						  filter: "isFile",
						  src : src,
						  rename : getPathFromDepth('public/css', src, depth)
					  }
				  ]
			  }
		  };
	  }

      if(isC)
      {
        data.cssmin = {
          compress: {
            files: [
              {
                expand: true,
                filter: 'isFile',
                src: src,
                rename: getPathFromDepth("public/css", src, depth)
              }]
          }
        };
        runMap.push("cssmin");
      }

      if(isC_M || isLess || isScss){
		  var destSrc = getPathFromDepth("public/css", src, depth)();

          data.copy = {
              main : {
                  files : [
                      {
                          filter: "isFile",
						  src :  src,
						  dest : destSrc,
						  options: {
							  process: function (content, srcpath) {
								  return content.replace(/[sad ]/g,"_");
							  }
						  }
                      }
                  ]
              }
          };

		  runMap.push("copy");
      }

      data.imagemin = {                          
        static: {
          files: [{
            expand: true,
            filter: 'isFile',
            src: [imgDir + "**/*.{jpg,png,gif}"],
            dest: "public/images/",
            rename: function(dest,src){
              var lIndex = src.lastIndexOf("/");
              lIndex = lIndex == -1 ? 0 : lIndex+1;
              
              return dest + src.slice(lIndex);
            }
          }]
        }
      };
      runMap.push("imagemin");

      grunt.config.data = data;
      grunt.task.run(runMap);
  });
  
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');

	grunt.registerTask('default', ['watch']);

  /**---------------下面是辅助函数，不要随意修改--------------**/
  function getPathFromDepth(dest,src,depth,needTag)
  {
    return function(){
      var i = depth,
          path = src,
          time = +new Date(),
          iIndex,lIndex,tag;

       console.log(sep);
      //去除头尾的空格
      src = src.replace(/^[\/\\]|[\/\\]$/g,'');

      //根据编译深度进行目标路径截取
      while(i--){
          iIndex = src.indexOf(sep);

          if(iIndex == -1){
            break;
          }
          src = src.slice(iIndex+1);

      }

      lIndex = src.lastIndexOf(".");

      //拼接含有时间戳的目标路径
      tag = needTag ? "-" + time : '';
      src = src.slice(0,lIndex) + tag + src.slice(lIndex);

        //console.log("dest + sep + src", dest + sep  + src);
      return dest + sep + src;
    }
  }

	function getExtFromSrc(src)
	{
		var lIndex,
			ext;

		//去除多余干扰字符
		src = src.replace(/[\/\\ ]+$/g,'');
		lIndex = src.lastIndexOf(".");

		ext = src.slice(lIndex + 1);

		if(ext === "map"){
			return src.slice(lIndex -3);
		}
		else if(lIndex != -1)
		{
		  return ext;
		}

	}

  function getDirPathFromSrc(src)
  {
    var lIndex;

    //去除多余干扰字符
    src = src.replace(/[\/\\ ]+$/g,'');
    lIndex = src.lastIndexOf(sep);


    return lIndex != -1 ? src.slice(0,lIndex+1) : "/";
  }

  function getCleanDir(src,depth)
  {
    var ext = getExtFromSrc(src),
        dest = "public/" + ext;

    src = src.replace(/[\/\\ ]+$/g,'');
    src = getPathFromDepth(dest,src,depth)();
    return src.split("-")[0] + "-*." + ext;

  }

};
