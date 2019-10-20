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
    if(!isset($_GET['pid'])) die("拒绝访问！");
    $title="Sakura";
    $show_buttons = TRUE;
?>
<?php 
    include_once 'style.php';
    include 'header.php';
    include_once 'database_util.php';
?>
    
<br/>
<div>
<?php 
    $conn = connect_db('localhost', 'web_user', '');
    $title = query_one($conn,'post_title','sakura.posts','post_id',$_GET['pid']);
    if($title == NULL) die("帖子不存在！");
    $content = query_one($conn,'post_content','sakura.posts','post_id',$_GET['pid']);
    echo "<p><b>".$title."</b></p>".$content."<p></p>";
?>
</div>

</body>
</html>