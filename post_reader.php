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
    if(!isset($_GET['pid'])) die("拒绝访问！");
    $title="Sakura";
    $show_buttons = TRUE;
    $post_title = query_one($conn,'post_title','sakura.posts','post_id',$_GET['pid']);
    if($post_title == NULL) die("帖子不存在！");
?>
<?php 
    include_once 'style.php';
    include 'header.php';    
?>
    
<br/>
<div>
<?php 

echo '<table border="1" id="reader"><tr>';
echo '<th><b></b></th>';
echo '<th><b></b></th>';
echo '<th><b>'.$post_title.'</b></th>';
echo '</tr>';

$layer = 0;
$post_uid = query_one($conn,'post_uid','sakura.posts','post_id',$_GET['pid']);
$post_user = query_one($conn,'user_nickname','sakura.user_info','user_id',$post_uid);
$content = query_one($conn,'post_content','sakura.posts','post_id',$_GET['pid']);
$post_timestamp = query_one($conn,'post_createtime','sakura.posts','post_id',$_GET['pid']);
$post_time = date('Y-n-j H:i:s',$post_timestamp);
if($layer%2 == 0) echo '<tr class="posteven">';
else echo '<tr class="postodd">';
echo '<td width="5%">'.$layer.'</td>';
echo '<td width="20%"><a href="/user_space.php?uid='.$post_uid.'">'.$post_user.'</a><br/>';
echo '<p>'.$post_time.'</p></td>';
echo '<td width="75%">'.$content.'</td>';
echo '</tr>';  

$sql = "SELECT * FROM sakura.reply WHERE reply_pid = ".$_GET['pid']." AND reply_state = 1 ORDER BY reply_createtime ASC";
$reply_val = mysqli_query($conn,$sql);
if(! $reply_val)
die("查询数据库失败：".mysqli_error($conn));
while($row = mysqli_fetch_array($reply_val))
{ 
    $layer += 1;
    if($layer%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="5%">'.$layer.'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[1]);
    $createtime = date('Y-n-j H:i:s',$row[3]);
    echo '<td width="20%"><a href="/user_space.php?uid='.$row[1].'">'.$user_nickname.'</a><br/>';
    echo '<p>'.$createtime.'</p></td>';
    echo '<td width="75%">'.$row[4].'</td>';
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
    echo '<a href="/editor.php?pid='.$_GET['pid'].'" class="edit_btn">回帖</a>';
}
?>
</div>

</body>
</html>