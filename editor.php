<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura</title>
</head>

<body>
<?php 
    include_once 'database_util.php';
?>
<?php 
    $bid = 1;
    if(isset($_GET['bid'])) 
    {
        $bid = $_GET['bid'];
        $boardname = query_one($conn,'board_name','sakura.board','board_id',$bid);
        if($boardname == NULL) die("版面不存在！"); 
    }
    
    if(isset($_GET['pid'])) 
    {
        $pid = $_GET['pid'];
        $post_title = query_one($conn,'post_title','sakura.posts','post_id',$pid);
        if($post_title == NULL) die("帖子不存在！");
    }
    
    if(isset($_GET['bid']) && isset($_GET['pid'])) die("访问错误！");
    if(!isset($_GET['bid']) && !isset($_GET['pid'])) die("访问错误！"); // 二者有且仅有一个有值
    
    $title="Sakura";
    $show_buttons = TRUE;
?>

<div>
    <?php
    if(isset($_POST['call']))
    {
        if(isset($_GET['bid']))
        {
            if($_POST['call']=="31")
            {
                $time = time();
                $state = '1';
                if(isset($_POST['replyable']) && !isset($_POST['top'])) $state = '3';
                if(isset($_POST['replyable']) && isset($_POST['top'])) $state = '4';
                if(!isset($_POST['replyable']) && isset($_POST['top'])) $state = '5';
                $content = $_POST['content'];
                $content = str_replace("\n","<br/>",$content); // 在网页端正确显示换行符
                if(isset($_FILES["files"]) && $_FILES["files"]["name"][0]!='')
                {
                    $content = $content.'<br/><br/>附加文件：';
                    for($i=0;$i<count($_FILES["files"]["name"]);$i++) // 依次上传文件
                    {
                        $division = pathinfo($_FILES['files']['name'][$i]);
                        $extensionName = $division['extension']; 
                        $file_url = 'files/'.md5(uniqid(microtime(true),true)).'.'.$extensionName;
                        move_uploaded_file($_FILES["files"]["tmp_name"][$i], $file_url);
                        $content = $content.'<br/><a href="/'.$file_url.'">'.$_FILES["files"]["name"][$i].'</a>';
                    }
                }
                $sql = "insert into sakura.posts (post_title,post_bid,post_uid,post_createtime,post_updatetime,"
                        ."post_content,post_state) value ('".$_POST['title']."',".$bid.",".$_SESSION['uid']
                        .",".$time.",".$time.",'".$content."',".$state.")";
                execute_sql($conn, $sql);
            }
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            array_splice($_FILES, 0, count($_POST));
            header('Location: index.php?bid='.$bid);
        }
        if(isset($_GET['pid']))
        {
            if($_POST['call']=="31")
            {
                $time = time();
                $state = '1';
                $content = $_POST['content'];
                $content = str_replace("\n","<br/>",$content); // 在网页端正确显示换行符
                $content = $_POST['content'];
                $content = str_replace("\n","<br/>",$content); // 在网页端正确显示换行符
                if(isset($_FILES["files"]) && $_FILES["files"]["name"][0]!='')
                {
                    $content = $content.'<br/><br/>附加文件：';
                    for($i=0;$i<count($_FILES["files"]["name"]);$i++) // 依次上传文件
                    {
                        $division = pathinfo($_FILES['files']['name'][$i]);
                        $extensionName = $division['extension']; 
                        $file_url = 'files/'.md5(uniqid(microtime(true),true)).'.'.$extensionName;
                        move_uploaded_file($_FILES["files"]["tmp_name"][$i], $file_url);
                        $content = $content.'<br/><a href="/'.$file_url.'">'.$_FILES["files"]["name"][$i].'</a>';
                    }
                }
                $sql = "insert into sakura.reply (reply_uid,reply_pid,reply_createtime,reply_content,reply_state) "
                        ."value (".$_SESSION['uid'].",".$_GET['pid'].",".$time
                        .",'".$content."',".$state.")";
                execute_sql($conn, $sql);
                $sql = "UPDATE sakura.posts SET post_updatetime = ".$time." WHERE post_id = ".$_GET['pid'];
                execute_sql($conn, $sql);
            }
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: post_reader.php?pid='.$_GET['pid']);
        }
    }
    
    include_once 'style.php';
    include 'header.php';  
?>
</div>

<br />
<div id="editor" class="editor">
<?php
if($_SESSION['uid'] == 0)
{
    die("请先登录！");
}
if(isset($_GET['bid']) && $bid==1)
{
    if(! find($conn,'uid','sakura.manage','bid','1',$_SESSION['uid']))
        die("访问错误：权限不足");
}
if(isset($_GET['pid']))
{
    $state = query_one($conn,'post_state','sakura.posts','post_id',$_GET['pid']);
    if($state == 3 && $state == 5)
        die("当前主题帖不可回复！");
}
echo '<form method="post" action="" enctype="multipart/form-data">';
if(isset($_GET['bid']))
    echo '帖子标题<input type="text" name="title" required oninvalid="setCustomValidity('."'不可为空'".');" oninput="setCustomValidity('."''".')"/>';
echo '<br />'.
    '<textarea cols="50" rows="10" name="content"></textarea>';
if(isset($_GET['bid']))
{
    echo '<label><input type="checkbox" name="replyable" value="0" >不可回复</label>';
    if(find($conn,'uid','sakura.manage','bid','1',$_SESSION['uid']) || 
        find($conn,'uid','sakura.manage','bid',$bid,$_SESSION['uid'])) // 版面管理员可发置顶帖
        echo '<label><input type="checkbox" name="top" value="0" >置顶</label>';
}  
echo '<input type="hidden" name="call" value="31"/>'.
    '<p style="display:inline-block">上传附件：请一次性选择需要上传的所有文件</p>'.
    '<input type="file" name="files[]" multiple=""/> ';
if(isset($_GET['bid']))
{
    echo '<input type="submit" value="发帖"/>';
}  
if(isset($_GET['pid']))
{
    echo '<input type="submit" value="回帖"/>';
}  
echo '</form>';
?>
</div>

</body>
</html>