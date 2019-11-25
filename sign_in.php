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
    include_once 'database_util.php';
?>
    
<?php
    if(isset($_POST['call']) and $_POST['call']=="13")
    {
        $uid = check_usrpsw($conn,$_POST['user_name'],$_POST['user_pwd']);
        if($uid == NULL) die("账号或密码错误！");
        $_SESSION['uid'] = $uid;
        header('Location: index.php');
    }
    include_once 'style.php';
    include 'header.php';
?>

<br/>
<div class="form">
    <?php
        $s1 = 'oninvalid="setCustomValidity('."'";
        $s2 = "'".')" oninput="setCustomValidity('."''".')"';
        if(isset($_POST['user_name']) && isset($_POST['user_pwd']))
        {
            echo '<form method="post" action="">
                帐号: <input type="text" class="login" name="user_name" value="'.$_POST['user_name'].
                    '" required '.$s1.'请填写账号'.$s2.'/>
                密码: <input type="password" class="login" name="user_pwd" value="'.$_POST['user_pwd'].
                    '" required '.$s1.'请填写密码'.$s2.'/>
                <input type="submit" class="login" value="登录"/>
                <input type="hidden" name="call" value="13"/>
                </form>';
        }
        else
        {
            echo '<form method="post" action="">
                帐号: <input type="text" class="login" name="user_name" required '.$s1.'请填写账号'.$s2.'/>
                密码: <input type="password" class="login" name="user_pwd" required '.$s1.'请填写密码'.$s2.'/>
                <input type="submit" class="login" value="登录"/>
                <input type="hidden" name="call" value="13"/>
                </form>';
        }
    ?>
</div>

</body>
</html>