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
    if(!isset($_GET['uid'])) die("拒绝访问！");
?>
<?php 
    $title="Sakura";
    $show_buttons = TRUE;
?>
<?php 
    include 'header.php';
    include_once 'database_util.php';
?>

<?php 
    $conn = connect_db('localhost', 'root', '');
    $str_uid = strval($_GET['uid']);
    $user_name = qurey_one($conn,'user_name','sakura.user_info',
            'user_id',$str_uid);
    $nickname = qurey_one($conn,'user_nickname','sakura.user_info',
            'user_id',$str_uid);
    echo '<p><font color="red">'.$nickname.'的个人主页</font></p>';
    echo '<p>账号：'.$user_name.'</p>';
    echo '<p>uid：'.$str_uid.'</p>';
    
    if($_SESSION['uid'] == $_GET['uid'])
    {
        $email = qurey_one($conn,'user_email','sakura.user_info',
            'user_id',$str_uid);
        echo '<p>邮箱：'.$email.'</p>';
    }
?>

</body>
</html>