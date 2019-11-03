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
    include_once 'style.php';
    include_once 'database_util.php';
?>
<?php 
    if(!isset($_GET['pid'])) die("拒绝访问！");
    $title="Sakura";
    $show_buttons = TRUE;
    $conn = connect_db('localhost', 'web_user', '');
    $post_title = query_one($conn,'post_title','sakura.posts','post_id',$_GET['pid']);
    if($post_title == NULL) die("帖子不存在！");
?>
<?php 
    include 'header.php';    
?>
    
<div>
    <?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="32")
        {
            $conn = connect_db('localhost', 'web_user', '');
            $time = time();
            $state = '1';
            $sql = "insert into sakura.reply (reply_uid,reply_pid,reply_createtime,reply_content,reply_state) "
                    ."value (".$_SESSION['uid'].",".$_GET['pid'].",".$time
                    .",'".$_POST['content']."',".$state.")";
            execute_sql($conn, $sql);
            $sql = "UPDATE sakura.posts SET post_updatetime = ".$time." WHERE post_id = ".$_GET['pid'];
            execute_sql($conn, $sql);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: post_reader.php?pid='.$_GET['pid']);
    }
?>
</div>
    
<br/>
<div>
<?php 
echo "<p><b>".$post_title."</b></p>";

echo '<table border="1"><tr>';
echo '<td><b>楼层数</b></td>';
echo '<td><b>发帖用户</b></td>';
echo '<td><b>内容</b></td>';
echo '<td><b>发帖时间</b></td>';
echo '</tr>';

$layer = 0;
$post_uid = query_one($conn,'post_uid','sakura.posts','post_id',$_GET['pid']);
$post_user = query_one($conn,'user_nickname','sakura.user_info','user_id',$post_uid);
$content = query_one($conn,'post_content','sakura.posts','post_id',$_GET['pid']);
$post_timestamp = query_one($conn,'post_createtime','sakura.posts','post_id',$_GET['pid']);
$post_time = date('Y-n-j H:i:s',$post_timestamp);
echo '<tr>';
echo '<td>'.$layer.'</td>';
echo '<td><a href="/user_space.php?uid='.$post_uid.'">'.$post_user.'</a></td>';
echo '<td>'.$content.'</td>';
echo '<td>'.$post_time.'</td>';
echo '</tr>';  

$sql = "SELECT * FROM sakura.reply WHERE reply_pid = ".$_GET['pid']." AND reply_state = 1 ORDER BY reply_createtime ASC";
$reply_val = mysqli_query($conn,$sql);
if(! $reply_val)
die("查询数据库失败：".mysqli_error($conn));
while($row = mysqli_fetch_array($reply_val))
{
    $layer += 1;
    echo '<tr>';
    echo '<td>'.$layer.'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[1]);
    echo '<td><a href="/user_space.php?uid='.$row[1].'">'.$user_nickname.'</a></td>';
    echo '<td>'.$row[4].'</td>';
    $createtime = date('Y-n-j H:i:s',$row[3]);
    echo '<td>'.$createtime.'</td>';
    echo '</tr>';      
}

echo '</table>';
?>
</div>

<br />
<div>
<?php
$post_state = query_one($conn,'post_state','sakura.posts','post_id',$_GET['pid']);
if($post_state <= 3 && $_SESSION['uid'] != 0)
{
    echo '<form method="post" action="">'.
        '<textarea cols="50" rows="10" name="content"></textarea>'.
        '<input type="submit" value="发表回复"/>'.
        '<input type="hidden" name="call" value="32"/>'.
        '</form>';
}
?>
</div>

</body>
</html>