<?php
// 文件管理
    session_start();
    $config = require('../config.php');
    require('../functions.php');
    force_login();  // 强制登录
    // 取得网站根目录
    $base_url = get_base_url("/admin/file.php");
    
    $search = $_GET['s'];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>文件管理 | bp3</title>
    <link href="../favicon.ico" rel="shortcut icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <link href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <!--<script src="../js/spark-md5.js"></script>-->
    <script src="https://cdn.bootcdn.net/ajax/libs/qs/6.10.3/qs.js"></script>
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
            <li class="active"><a href="./file.php">文件管理<i class="fa fa-th-large" aria-hidden="true"></i><span class="sr-only">(current)</span></a></li>
            <li><a href="./settings.php">修改设置<i class="fa fa-cog"></i></a></li>
            <li><a href="./help.php">帮助与支持<i class="fa fa-question-circle" aria-hidden="true"></i></a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="../">前台<i class="fa fa-home"></i></a></li>
            <li><a href="./logout.php">注销<i class="fa fa-sign-out" aria-hidden="true"></i></i></a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
  
    </div><div id="myheader" class="container-fluid">
        <div class="container jumbotron">
            <div class="row">
            <?php // 说明为dir查询
                if(empty($search)){ ?>
                <div class="col-xs-12" style="margin-bottom:10px">
                    <div class="btn-group">
                        <input id="upload" type="file" class="hidden"/>
                        <button class="btn btn-default" onclick="$('#upload').trigger('click');">上传文件</button>
                        <button class="btn btn-default" onclick="mkdir()">新建文件夹</button>
                    </div>
                </div>
            <?php } ?>
                <div class="col-xs-12">
                    <ol class="breadcrumb">
 <?php
    // 捕获dir查询参数
    $dir = $_GET['dir'];
    echo "<li><a href='./file.php'>Home</a></li>";
    if(!empty($dir)){ // 非根目录，一个或多个
        $dir = urldecode($dir);
        $dirs = explode('/',$dir); // 取得路径
        $dir_path = '';  // 新的访问路径
        $dir_paths = [null,]; // 存储新路径组
        for($i=1;$i<count($dirs);$i++){
            $dir_path.='/';
            $dir_path.=$dirs[$i];
            $dir_paths[$i] = $dir_path;
            $dir_link = urlencode($dir_path);
            echo "<li><a href='?dir=$dir_link'>$dirs[$i]</a></li>";
        }
    }
?>
                    </ol>
                </div>
            </div>
            <form>
                <div class="row">
                    <div class="col-xs-12">
                    <div class="search-wraper" role="search">
                        <div class="input-group">
                          <input type="text" name="s" class="form-control" placeholder="<?php 
                          if($_GET['s'])
                            echo '正在搜索：'.$_GET['s'];
                            else echo '搜索文件';?>" required="required">
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">Go!</button>
                          </span>
                        </div><!-- /input-group -->
                    </div>
                    </div><!--col-xs-12-->
              </div><!--row-->
            </form>
            </div>
        </div>
    </header>
    
<main>
    <div class="container">
<div class="bs-example" data-example-id="hoverable-table">
    <table class="table table-bordered table-responsive">
<?php
    // 获取查询方式，调用查询方法，优先query，参数s
    $predir = '';
    $access_token = $config['identify']['access_token'];
    $has_more = false; // 全局共享变量，是否有下一页
    $page;  // 当前页数
    if($search){ // 取得query参数
        $key = $_GET['s'];
        $page = $_GET['page']; // 捕获分页参数
        if(empty($page)){$page=1;}
        $url = "http://pan.baidu.com/rest/2.0/xpan/file?dir=$predir&access_token=$access_token&web=1&recursion=1&page=$page&num=20&method=search&key=$key";

        $opt = easy_build_http("GET",["User-Agent:pan.baidu.com"]);
        $result = easy_file_get_content($url,$opt);
        
        $json = json_decode($result);
        $has_more = $json->has_more;
        if(!$json->list){
            echo("这儿似乎什么也没有...");
        }else{
            echo "<thead class='active'><tr><th></th><th>文件<i class='glyphicon glyphicon-chevron-down'></i></th><th>大小<i class='glyphicon glyphicon-chevron-down'></i></th><th>操作<i class='glyphicon glyphicon-chevron-down'></i></th></tr></thead><tbody>";
            // var_dump($json);
            foreach ($json->list as $row){
                if($row->isdir==1){
                    // 去掉前缀
                    $path = substr($row->path,strlen($predir));
                    $encode_path = urlencode($path);
                    $title = $row->path;
                    $delete = "<button class='btn btn-danger' path='$path' onclick='del(event)'>删除</button>";
                    if($path=="/apps"){$delete="";}
                    $tree = "<td class='br'><div class='m-btns btn-group'><a class='btn btn-default' onclick='checkTree(\"/admin/tree.php?base_dir=$encode_path\")'>资源树</a><a class='btn btn-default' target='_blank' href='$base_url/admin/imglist.php?path=$encode_path'>相册</a><button path='$path' onclick='rename(event)' class='btn btn-default'>重命名</button>$delete</div></td>";
                 echo "<tr><th scope='row'><i class='glyphicon glyphicon-folder-open'></i></th><td class='info br' colspan='2' ><a href='?dir=$encode_path' style='display:block'>$path</a></td>$tree</tr>";
                 }else{
                     $fsid = $row->fs_id;
                     $show_size = height_show_size($row->size);
                     $title = $row->path;
                 echo "<tr><th scope='row'><i class='glyphicon glyphicon-file'></i></th><td class='br'>$row->server_filename <span tip='$title' class='tip fa fa-question-circle-o'></span></td><td>$show_size</td>
          <td>
              <div class='m-btns btn-group' role='group' aria-label='...'>
              <a href='$base_url/dn.php?fsid=$fsid' type='button' class='btn btn-default'>下&nbsp;&nbsp;&nbsp;载</a>
              <a  target='_blank' href='$base_url/admin/dlink.php?fsid=$fsid' type='button' class='btn btn-default'>直链</a><button path='$path' onclick='rename(event)' class='btn btn-default'>重命名</button><button class='btn btn-danger' path='$path' onclick='del(event)'>删除</button>
              </div>
          </td>
        </tr>";
                }
            }
            echo "</tbody>";
        }
    }
    else{ // dir查询
        $dir = $_GET['dir'];
        if(!$dir){ // 根目录
            $dir = $predir;
            if($predir==""){
                $dir = "/";
            }
        }else{
            $dir = $predir.$dir;
        }
        $dir = urlencode($dir);
        $url = "https://pan.baidu.com/rest/2.0/xpan/file?method=list&dir=$dir&order=name&start=0&limit=100&web=web&folder=0&access_token=$access_token&desc=0";

        $opt = easy_build_http("GET",["User-Agent:pan.baidu.com"]);
        $result = easy_file_get_content($url,$opt);
        // var_dump($result);
        $json = json_decode($result);
        $baidu_icon = '<svg t="1635907111308" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2224" width="20" height="20"><path d="M513.12 523.2m-464 0a464 464 0 1 0 928 0 464 464 0 1 0-928 0Z" p-id="2225"></path><path d="M752 631.2a33.76 33.76 0 0 0 33.28-36.96 130.24 130.24 0 0 0-24-76 128 128 0 0 0-78.4-53.44c-12.16-2.72-24.64-3.84-37.44-5.76a132 132 0 0 0-42.4-106.24 128 128 0 0 0-109.6-34.56 133.6 133.6 0 0 0-113.28 142.4 134.08 134.08 0 0 0-138.08 119.52 128 128 0 0 0 36.64 106.24 128 128 0 0 0 112 39.52 119.52 119.52 0 0 0 68.8-29.92c19.2-17.76 36.8-37.12 55.2-55.84 30.72-32 61.12-63.2 92-94.56a66.56 66.56 0 0 1 112.8 49.12A33.76 33.76 0 0 0 752 631.2z m-376.32 29.28a66.72 66.72 0 1 1 64.96-66.08 66.08 66.08 0 0 1-64.96 66.08z m136.8-144a66.72 66.72 0 1 1 66.4-65.76 66.08 66.08 0 0 1-66.88 65.6zM744.8 663.52a32 32 0 0 0-33.92 32 33.44 33.44 0 0 0 32.96 33.76 33.92 33.92 0 0 0 33.28-32 32.96 32.96 0 0 0-32.32-33.76z" fill="#FFFFFF" p-id="2226"></path></svg>';
        echo("<thead><tr class='active'><th></th><th>文件<i class='glyphicon glyphicon-chevron-down'></i></th><th>大小<i class='glyphicon glyphicon-chevron-down'></i></th><th>操作<i class='glyphicon glyphicon-chevron-down'></i></th></tr></thead><tbody>");
        echo("<tr class='success'><td>$baidu_icon</td><td colspan='3'><a id='disk_page' href='https://pan.baidu.com/disk/home?#/all?path=$dir'  target='_blank' style='display:block'><i class='fa fa-arrow-circle-right'></i>百度网盘网页版当前目录</a></td></tr>");
        if(!$json->list){
            echo("<tr><td colspan='4' >这儿似乎什么都没有...</td></tr>");
            echo("</tbody>");
        }else{
            // var_dump($json);
            foreach ($json->list as $row){
                if($row->isdir==1){
                    // 去掉前缀
                    $path = substr($row->path,strlen($predir));
                    $encode_path = urlencode($path);
                    $delete = "<button class='btn btn-danger' path='$path' onclick='del(event)'>删除</button>";
                    if($path=="/apps"){$delete="";}
                    $tree = "<td class='br'><div class='m-btns btn-group'><a class='btn btn-default' onclick='checkTree(\"/admin/tree.php?base_dir=$encode_path\")'>资源树</a><a class='btn btn-default' target='_blank' href='$base_url/admin/imglist.php?path=$encode_path'>相册</a><button path='$path' onclick='rename(event)' class='btn btn-default'>重命名</button>$delete</div></td>";
                 echo "<tr><th scope='row'><i class='glyphicon glyphicon-folder-open'></i></th><td class='info' colspan='2' ><a href='?dir=$encode_path' style='display:block'>$row->server_filename</a></td>$tree</tr>";
                 }else{
                     $fsid = $row->fs_id;
                     $path = $row->path;
                     $show_size = height_show_size($row->size);
                 echo "<tr><th scope='row'><i class='glyphicon glyphicon-file'></i></th><td class='br'>$row->server_filename</td><td>$show_size</td>
          <td>
              <div class='m-btns btn-group' role='group' aria-label='...'>
              <a type='button' class='btn btn-default' href='$base_url/dn.php?fsid=$fsid'>下&nbsp;&nbsp;&nbsp;载</a>
              <a target='_blank' href='$base_url/admin/dlink.php?fsid=$fsid' type='button' class='btn btn-default'>直链</a><button path='$path' onclick='rename(event)' class='btn btn-default'>重命名</button><button class='btn btn-danger' path='$path' onclick='del(event)'>删除</button>
              </div>
          </td>
        </tr>";
                }
            }
        }
        echo "</tbody>";
    }
?>
    </table>
  </div>
    </div>

<div class="container">
    
<?php  // 查询时存在分页
    if($_GET['s'] and $has_more){
        $s = $_GET['s'];
        $prev_page = $page-1;
        $next_page = $page+1;
    echo "<ul class='pager'>";
    if($page>1){
        echo "<li><a href='./file.php?page=$prev_page&s=$s'>上一页</a></li>";
    }
    echo "<li><a>分页$page</a></li>";
    echo "<li><a href='./file.php?page=$next_page&s=$s'>下一页</a></li>";
    echo "</ul>";
    }
?>
    
</div>
</main>
<footer class="navbar navbar-default navbar-inverse copyright">
        <p class="text-center" style="color:#9d9d9d;margin-top:15px;">Copyright © bp3 <?php echo date('Y')?></p>
</footer>
<a href="javascript:(function(){window.scrollTo(0, 0);})();" title="返回顶部" id="back-to-top" style="display:none;position:fixed;right:10px;bottom:10px;background-color:rgb(95,99,104);box-sizing: border-box;cursor:pointer;text-align:center;"><i class="fa fa-angle-up" style="height:40px;width:40px;display:iniline—block;line-height:40px;color:#fff;"></i></a>
<style>
    .copyright{
        margin-bottom: 0px;
    }
    .br{
        word-break: break-all !important;
    }
    .tip{
        cursor: pointer;
    }
</style>
<script src="../js/clipboard.min.js"></script>
<script>
    function fixMobile(){
        if(document.body.clientWidth<768){
            let href = $('#disk_page').attr("href");
            if(href){
                href = href.replace('#/all?path=','#/dir/');
                $("#disk_page").attr("href",href);
            }
            $(".m-btns").addClass("btn-group-vertical");
        }
    }
    $(function () {
      fixMobile();
      if($(window).height()==$(document).height()){
        $(".copyright").addClass("navbar-fixed-bottom");
      }
      else{
        $(".copyright").removeClass(" navbar-fixed-bottom");
      }
      if(document.body.clientWidth<768){
        $(".m-btns").addClass("btn-group-vertical");
      }
    });
    $(window).scroll(function(){
        let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        if(scrollTop>50){
            $("#back-to-top").css("display","block");
        }else{
            $("#back-to-top").css("display","none");
        }
    });
    function message(text,type){
        let text_class = "";
        switch(type){
            
        case "default":
            text_class = "fa fa-comments";
            break;
        case "info":
            text_class = "fa fa-info-circle text-info";
            break;
        case "success":
            text_class = "fa fa-check-square-o text-success";
            break;
        case "warning":
            text_class = "fa fa-warning text-warning";
            break;
        case "error":
            text_class = "fa fa-close text-danger";
            break;
        default:
            throw "消息type错误，请传递default/info/success/warning/error中的任一种";
            break;
        }
        let msgs = $(".message");
        let len = msgs.length; 
        let end = 0;
        let baseHeight = 0;
        if(len>0){
            baseHeight =msgs.first().innerHeight()+20;
            let start = msgs.first().attr('no');
            end = +start+len;
        }
        let height = 100+end*baseHeight+"px";
        $(`<div no='${end}' id='msg-${end}' class='message ${text_class}' style='top: ${height};position: fixed;left: 50%;border: 1px solid #ddd;
        background-color:#bbb;transform: translate(-50%, -50%);font-size: 1.2em;padding: 1rem;z-index: 999;border-radius: 0.5rem;'>${text}</div>`).appendTo("body");
        let rmScript = `$("#msg-${end}").remove();`;
        setTimeout(rmScript,1500);
    }
    // 删除文件
    function del(e){
        let check = confirm("短期内可在百度网盘app回收站找回，请确认删除：");
        if(check){
            let path = $(e.target).attr("path");
            $.get("../controller/del_file.php",{"path":path},function(data){
                if(data.errno===0){
                    message("删除成功","success");
                    setTimeout("location.reload()",200);
                }else{
                    message("删除失败","error");
                }
            },"json")
        }
    }
    // 文件重命名
    function rename(e){
        let name = prompt("请输入文件新名称");
        if(!name){
            message("未输入名称","error");
            return;
        }
        name = name.trim();  // 前后不可有空格
        if(name==""){
            message("未输入名称","error");
            return;
        }
        if(name.length>255){
            message("名称不可大于255字符","error");
            return;
        }
        
        let path = $(e.target).attr("path");
        $.get("../controller/rename.php",{"path":path,"name":name},function(data){
            if(data.errno===0){
                message("重命名成功","success");
                setTimeout("location.reload()",200);
            }else{
                message("重命名失败","error");
            }
        },"json")
    }
    // 是否生成目录树
    function checkTree(url){
        let check = confirm("风险提示：请确认要尝试生成资源树，如果文件数据太多可能出现服务器卡死")
    	if(check){
    	    window.open(`${url}`);
    	}
    }
    // 提示完整目录
    $(".tip").click(function(){
        alert($(this).attr("tip"));
    });
    // 创建文件夹
    function mkdir(){
        let name = prompt("请输入文件夹名称");
        if(!name){
            message("未输入文件夹名称","error");
            return;
        }
        name = name.trim();  // 前后不可有空格
        if(name==""){
            message("未输入文件夹名称","error");
            return;
        }
        if(name.length>255){
            message("文件夹名称不可大于255字符","error");
            return;
        }
        for(let i=0;i<name.length;i++){
            let char = name.charAt(i);
            
            if(char ==':' || char== '<' || char=='>' || char=='|' || char=='*' || char=='?' || char=='/'){
                message("不能包含字符:<>|*?/","error");
                return;
            }
        }
        $.post("../controller/mkdir.php",{"name":name,"path":"<?php echo $dir;?>"},function(data){
            if(data.errno!=0){
                message("文件夹创建失败","error");
            }else{
                message("文件夹创建成功","success");
                setTimeout("location.reload()",200);
            }
        },"json");
    }
    $(function(){
    	let image = document.getElementById('upload');
    	image.onchange = function(){
    	    
    	    alert("正在做了...请尝试其他功能");
    	    this.value = "";
    	    return;
    	    
    		let files = this.files[0];//获取file组件中的文件
    		let name = files.name;//文件名
    		let size = files.size;//文件大小
    		let dir = <?php $decode_dir=urldecode($dir); echo "'$decode_dir'"; ?>;
    		if(dir!='/'){
    		    dir = dir+"/";
    		}
    		dir = dir+name;
    		dir = encodeURIComponent(dir); // 转码后的绝对地址（网盘中的地址）
    // 		alert(dir)
    		$.ajaxSettings.async = false;
    		// 预上传
    		let slice = 4194304;  // 分片大小为4MB
    		
    		let uploadid = "";
    		let md5_list = get_md5_list(files,size,slice);//取得各个分片md5
    		let res_md5_list = "";
    		let access_token = "";
    		$.post("./upload.php?method=precreate",{"path":dir,"size":size,"block_list":md5_list},function(data){
    		    if(data.errno==0){
    		        uploadid = data.uploadid;
    		        res_md5_list = data.block_list;
    		        access_token = data.access_token;
    		    }else{
    		        message("上传失败","error");
    		        return;
    		    }
    		},"json");
    // 		alert(uploadid)
    // 		alert(res_md5_list)
    // 		alert(access_token)
            // 处理预上传的特殊数据: teres_block_md5 ( array )
            if(res_md5_list.length==0){
                res_md5_list.push(0);
            }
    		// 上传文件
    		let upload_number = res_md5_list.length;
    // 		alert("共需上传分片个数: "+upload_number);
            let server_md5 = "["  // 拼接server_md5
            for(let i=0;i<upload_number;i++){
                let currentUrl = `https://d.pcs.baidu.com/rest/2.0/pcs/superfile2?method=upload&access_token=${access_token}&path=${dir}&type=tmpfile&uploadid=${uploadid}&partseq=${i}`
                let endSize = size<slice?size:slice
                let endIndex = i*slice+endSize
                let currentFile = files.slice(i*slice,endIndex);
                
                let formData = new FormData();
                
                formData.append('file', currentFile);
                formData.append('fastUrl', currentUrl);
                $.ajax({
                type: "POST",
                url: "./upload.php?method=upload",
                data: formData,
                contentType: false,
                processData: false,
                success: function(resdata){
                    // console.log(resdata)
                    resdata = $.parseJSON(resdata)
                    server_md5 += '"'
                    server_md5 += resdata.md5
                    server_md5 += '"'
                    
                    if(i<upload_number-1){
                        server_md5 += ","
                    }
                }
                 })
            }
            server_md5 += "]"
            // alert(server_md5);
    		// 创建文件
    		$.post("./upload.php?method=create",{"path":dir,"size":size,"block_list":server_md5,"$":uploadid},function(data){
    		    alert(data);
    		});
            this.value = "";  // 清空input缓存
    	}
    })
    
    // 获取分片的md5
    function get_md5_list(files,size,slice){
        let number = Math.ceil(size/slice);
        let md5List = "["
        for(let i=0;i<number;i++){
            md5List += '"'
            md5List += guid()
            md5List += '"'
            if(i<number-1){
                md5List += ","
            }
        }
        md5List += "]"
        return md5List;
    }
    
    function slice_md5(file,slice){  // 真实md5，可支持秒传，由于一些问题弃用
        var spark = new SparkMD5.ArrayBuffer(),
            fileReader = new FileReader();
        var hash = '';

        fileReader.onload = function (e) {
            spark.append(e.target.result);                   
            hash = spark.end();
            console.log(hash);
            return hash;
        };
        fileReader.readAsArrayBuffer(file);
    };
    
    //用于生成uuid，伪造文件分片md5，不支持秒传
    function S4() {
        return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    }
    function guid() {
        return (S4()+S4()+S4()+S4()+S4()+S4()+S4()+S4());
    }

</script>
</body>
</html>