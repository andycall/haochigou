/**
 * @author zhaoyang
 * @date 2014-11-1
 * @desc 服务端自动代码更新
 */
var exec = require("child_process").exec,
    fs = require("fs"),
    stdout = process.stdout,
    stdin = process.stdin,
    dirPath = "./log",
    i = 0,
    path,interval;

stdout.write("-------------------Server Listener-------------------\n");
stdout.write("Repository Path: ");

stdin.resume();
stdin.setEncoding("utf-8");

//相关信息填写
stdin.on("data",function(buf){
    i++;
    switch(i){
        case 1:
            path = buf.replace("\n","");
            stdout.write("\nInterval(min): ");
            break;
        case 2:
            interval = Number(buf);
            stdin.pause();
            stdout.write("\nNow listening.....\n");
            createLogDir();
            pullCode("dev");
            break;
    }
});

//因为不得知的原因中断，则捕获最后的异常事件，重启服务
process.on('uncaughtException', function(err) {
    var time = new Date().toString(),
        date = getDate(),
        path = dirPath+"/"+ date +".log";

    console.warn(time+"   "+err)
    fs.appendFileSync(path,time+"   "+err);
    setTimeout(pullCode,5000);
});

//拉代码
function pullCode(branch){
    exec("cd "+path+" && git pull",function(error, stdout, stderr){
        var time = new Date().toString(),
            date = getDate(),
            path = dirPath+"/"+ date +".log";
        if(error != null){
            console.warn(time+"   "+stderr)
            fs.existsSync(path) && fs.appendFileSync(path,time+"   "+stderr);
        }else{
           fs.appendFileSync(path,time+"   "+stdout);
        }

        setTimeout(pullCode,interval * 1000 * 60);
    });
}

//创建log文件夹
function createLogDir()
{
    if(!fs.existsSync(dirPath))
    {
        fs.mkdirSync(dirPath);
    }else if(!fs.statSync(dirPath).isDirectory())
    {
        fs.mkdirSync(dirPath);
    }
}

//当前日期
function getDate()
{
    var t = new Date();
    return t.getFullYear() + "-" + (t.getMonth()+1) + "-" + t.getDate();
}
