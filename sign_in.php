<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura - 登录页面</title>
</head>

<body>
<?php 
    $title="Sakura";
    $show_buttons = FALSE;
?>
<?php 
    include 'header.php';
    include_once 'database_util.php';
?>
<p>SakuraSakura Ai Tai Yo ~</p>

<?php 
    if(isset($_POST['user_name']) && isset($_POST['user_pwd']))
    {
        echo '<form method="post" action="">
            帐号: <input type="text" name="user_name" value="'.$_POST['user_name'].
                '" required oninvalid="setCustomValidity('."'".'请填写账号'."'".');"/>
            密码: <input type="password" name="user_pwd" value="'.$_POST['user_pwd'].
                '" required oninvalid="setCustomValidity('."'".'请填写密码'."'".');"/>
            <input type="submit" value="登录"/>
            <input type="hidden" name="call" value="13"/>
            </form>';
    }
    else
    {
        echo '<form method="post" action="">
            帐号: <input type="text" name="user_name" required oninvalid="setCustomValidity('."'".'请填写账号'."'".');"/>
            密码: <input type="password" name="user_pwd" required oninvalid="setCustomValidity('."'".'请填写密码'."'".');"/>
            <input type="submit" value="登录"/>
            <input type="hidden" name="call" value="13"/>
            </form>';
    }
?>

<?php
    if(isset($_POST['call']) and $_POST['call']=="13")
    {
        $conn = connect_db('localhost', 'root', '');
        $uid = check_usrpsw($conn,$_POST['user_name'],$_POST['user_pwd']);
        if($uid == NULL) die("账号或密码错误！");
        $_SESSION['uid'] = $uid;
        header('Location: index.php');
    }
?>

</body>
</html>