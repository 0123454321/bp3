<?php
// 文件管理
    session_start();
    $config = require('../config.php');
    require_once("../functions.php");
    force_login();//强制登录
    // 获取open地址
    $index_url = get_base_url("/admin/help.php");
    $open_url = $index_url."/open.php";
    // 授权地址
    $grant = $index_url."/grant/";
    $grant2 = $index_url."/grant2/";
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>帮助与支持 | bp3</title>
    <link href="../favicon.ico" rel="shortcut icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/clipboard.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <link href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet"/>

</head>
<body style="background-color:rgb(231,231,231);">
 
    <header >
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="./">管理系统</a>
        </div>
    
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
         <ul class="nav navbar-nav">
          </ul>
          <ul class="nav navbar-nav">
            <li><a href="./file.php">文件管理<i class="fa fa-th-large" aria-hidden="true"></i><span class="sr-only">(current)</span></a></li>
            <li><a href="./settings.php">修改设置<i class="fa fa-cog"></i></a></li>
            <li class="active"><a href="./help.php">帮助与支持<i class="fa fa-question-circle" aria-hidden="true"></i></a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="../">前台<i class="fa fa-home"></i></a></li>
            <li><a href="./logout.php">注销<i class="fa fa-sign-out" aria-hidden="true"></i></i></a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
  
    </div>
    </header>
<main>
<div class="container help">
    
    <h3>当前版本</h3>
    <p>您当前使用的版本号是：<?php echo $config['version'];?>,可点击 <button onclick="check_update()" class="btn btn-primary">检测更新</button></p>
    <div id="latest_tip">
        <table class="table table-bordered table-responsive">
        <tr><td>最新下载地址</td><td><a href="https://github.com/zhufenghua1998/bp3/archive/refs/heads/main.zip">https://github.com/zhufenghua1998/bp3/archive/refs/heads/main.zip</a></td></tr>            
        </table>
    </div>
    <p>如需更新，请 <input id="upload" type="file" class="hidden"/><input type="button" class="btn btn-primary" value="点击上传" onclick="$('#upload').trigger('click');"/> 下载好的最新版代码，后台会自动完成更新</p>
      <div id="show_progress" class="progress-area" class="hidden">
        进度
        <div class="progress">
          <div class="progress-bar" id="progress" role="progressbar"  aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
        <div>
          <p id="time"></p>
        </div>
      </div>
      
    <h3>如何配置前台开放目录？</h3>
    <p>在设置中，写上要省略的前置目录，例如：</p>
    <ul>
        <li>开放根目录：留空</li>
        <li>开放/apps目录：填写/apps，注意结尾不要/</li>
    </ul>
    
    <h3>如何重置系统？</h3>
    <p>请把根目录下config.php文件删除，会重置本系统</p>
    <h3>账户已经锁定？</h3>
    <p>为防止暴力破解，一旦账户密码连续错误3次，将会锁定，暂需手动恢复</p>
    <p>请ftp后，编辑根目录下的config.php文件，把 user => chance 选项设置为3</p>
    
    <h3>忘记账户密码？</h3>
    <p>请ftp后，打开根目录下的config.php文件，其中user=>name为账户名，user=>pwd为密码。</p>
    
    <h3>本系统授权服务器地址？</h3>
    <p>免app授权地址</p>
    <pre><code><?php echo $grant;?></code></pre>
    <p>内置app授权地址</p>
    <pre><code><?php echo $grant2;?></code></pre>
    
    <h3>开发者获取token接口</h3>
    <p>bp3当前使用的access_token可以被其他程序获取，为了安全起见仅本机程序(同IP地址)可以获取，地址如下：</p>
    <pre><?php echo $open_url; ?></pre>
    <p>例如，你可以使用以下python代码获取access_token</p>
    <pre><code>from urllib import request
    
resp = request.urlopen('<?php echo $open_url;?>')
print(resp.read().decode())</code></pre>
    <p>如果你希望直接取得token用于测试，请<a href="../open.php" target="_blank">点击查看</a>。</p>
    <h3>其他问题</h3>
    <p>参阅百度网盘开发者官方文档：<a href="https://pan.baidu.com/union/doc/" target="_blank">百度网盘开发者文档</a></p>
    <p>如需帮助，请在github发布issure，或者QQ交流群：1150064636</p>
</div>

</main>
<footer class="navbar navbar-default navbar-fixed-bottom navbar-inverse copyright">
<p class="text-center" style="color:#9d9d9d;margin-top:15px;">Copyright © bp3 <?php echo date('Y')?></p>
</footer>
<style>
    .copyright{
        margin-bottom: 0px;
    }
    .help{
        font-size: 1.1em;
    }
    .help p{
        text-indent: 2em;
    }
    td{
        word-break: break-all;
    }
</style>
<script>
    var progress = document.querySelector('#progress');
    var time = document.querySelector('#time');
    var xhr = new XMLHttpRequest();
    var loaded = 0, ot = 0, total = 0, oloaded = 0 ;
    
    $(function () {
      if($(window).height()==$(document).height()){
        $(".copyright").addClass("navbar-fixed-bottom");
      }
      else{
        $(".copyright").removeClass(" navbar-fixed-bottom");
      }
      
	let file = document.getElementById('upload');
	file.onchange = function(){
        
        if(!file.files[0]) {
            return
        }
        let ext,idx;   
        let imgName = file.value;
        idx = imgName.lastIndexOf(".");   
        if (idx != -1){   
        ext = imgName.substr(idx+1).toUpperCase();   
        ext = ext.toLowerCase(); 
        // alert("ext="+ext);
        if (ext != 'zip'){
            alert("只能上传.zip 类型的文件!"); 
            return;  
        }
        }
        if(file.files[0].size>20971520) {
            alert('文件不得超过20M')
            return
        }
        var formData = new FormData();
        formData.append('file', file.files[0]);
        xhr.onload = uploadSuccess;
        xhr.upload.onprogress = setProgress;
        xhr.open('post', '../update/up_core.php', true);
        xhr.send(formData);
	}
        // 成功上传
        function uploadSuccess(event) {
          if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText);
            setTimeout("location.reload()", 2000 );
          }
        }
        // 进度条
        function setProgress(event) {
          // event.total是需要传输的总字节，event.loaded是已经传输的字节。如果event.lengthComputable不为真，则event.total等于0
          if (event.lengthComputable) {//
              loaded = event.loaded
              total = event.total
              var complete = (event.loaded / event.total * 100).toFixed(1);
              progress.innerHTML = Math.round(complete) + "%";
              progress.style.width = complete + '%';
          }
          var time = document.getElementById("time");
          var nt = new Date().getTime();//获取当前时间
          var pertime = (nt-ot)/1000; //计算出上次调用该方法时到现在的时间差，单位为s
          ot = new Date().getTime(); //重新赋值时间，用于下次计算
          var perload = event.loaded - oloaded; //计算该分段上传的文件大小，单位b      
          oloaded = event.loaded;//重新赋值已上传文件大小，用以下次计算
          //上传速度计算
          var speed = perload/pertime;//单位b/s
          var bspeed = speed;
          var units = 'b/s';//单位名称
          if(speed/1024>1){
              speed = speed/1024;
              units = 'k/s';
          }
          if(speed/1024>1){
              speed = speed/1024;
              units = 'M/s';
          }
          speed = speed.toFixed(1);
          //剩余时间
          var resttime = ((event.total-event.loaded)/bspeed).toFixed(1);
          time.innerHTML = '传输速度：'+speed+units+'，剩余时间：'+resttime+'s';
          // if(bspeed==0) time.innerHTML = '上传已取消';
        }
        xhr.onloadstart = function(){
          console.log("上传开始");  
          $("#show_progress").removeClass("hidden");
        }
        xhr.ontimeout = function(){
          console.log('上传超时.');
        }
        // xhr.timeout = 50000; // 默认为0 没有时间限制
        // xhr.onabort = function(){
        //   console.log("The transfer has been canceled by the user.");
        // }
        xhr.onerror = function(){
          console.log("上传错误，可能是断网了，也可能是断请求服务了.");  // 这里存在异步传输问题
          return
        }
        xhr.onloadend = function(){
          console.log("请求结束"); // 发送上传的请求，至于有没有上传成功，不清楚，可能失败 成功，这里只是请求结束了
        }
    });
    // 复制代码
    $("pre").mouseenter(function (e) {
        var _that = $(this);
        _that.css("position", "relative");
        _that.addClass("activePre");
        var copyBtn = _that.find('.copyBtn');
        if (!copyBtn || copyBtn.length <= 0) {
            var copyBtn = '<span class="copyBtn" style="position:absolute;top:2px;right:2px;z-index:999;padding:2px;font-size:13px;color:black;background-color: white;cursor: pointer;" onclick="copyCode()">Copy</span>';
            _that.append(copyBtn);
        }
    }).mouseleave(function (e) {
        var _that = $(this);
        var copyBtn = _that.find('.copyBtn');
        var copyBtnHover = _that.find('.copyBtn:hover');
        if (copyBtnHover.length == 0) {
            copyBtn.remove();
            _that.removeClass("activePre");
        }
    });
    function copyCode() {
        var activePre = $(".activePre");
        activePre = activePre[0];
        var code = activePre.firstChild;
        if(code.nodeName=="CODE"){
            activePre = code;
        }
        var clone = $(activePre).clone();
        clone.find('.copyBtn').remove();
        var clipboard = new ClipboardJS('.copyBtn', {
            text: function () {
                return clone.text();
            }
        });
        clipboard.on("success", function (e) {
            $(".copyBtn").html("Copied!");
            clipboard.destroy();
            clone.remove();
        });

        clipboard.on("error", function (e) {
            clipboard.destroy();
            clone.remove();
        });
    }
    function check_update(){
        $.post("../controller/check_update.php",function(data){
            
            if(!data.tag_name){
                
                $("#latest_tip").empty();
                let str = `<table class="table table-bordered table-responsive">`;
                
                str += `<tr><td>错误描述：</td><td>${new Date().toLocaleString()},本次github网络链接失败</td></tr>`
                str += `        <tr><td>最新下载地址</td><td><a href="https://github.com/zhufenghua1998/bp3/archive/refs/heads/main.zip">https://github.com/zhufenghua1998/bp3/archive/refs/heads/main.zip</a></td></tr>  `
                str += `</table>`;
                $("#latest_tip").append(str);
                return;  // 网络连接失败
            }
            
            $("#latest_tip").empty();
            let str = `<table class="table table-bordered table-responsive">`;
            str += `<tr><td>最新版本</td><td>${data.tag_name}</td></tr>`;
            str += `<tr><td>版本类型</td><td>${data.prerelease?"测试版":"稳定版"}</td></tr>`;
            
            let version_body = data.body;
            version_body = version_body.replace(/\r\n/g,"<br>");
            
            str += `<tr><td>版本描述：</td><td>${version_body}</td></tr>`
            str += `<tr><td>更新时间</td><td>${data.published_at}</td></tr>`
            str += `<tr><td>最新下载地址</td><td><a href="https://github.com/zhufenghua1998/bp3/archive/refs/heads/main.zip">https://github.com/zhufenghua1998/bp3/archive/refs/heads/main.zip</a></td></tr>  `
            str += `</table>`;
            $("#latest_tip").append(str);
        },"json");
    }
    
</script>
</body>
</html>