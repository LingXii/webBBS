<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura - 注册页面</title>
</head>

<body>
<?php 
    $title="Sakura";
    $show_buttons = FALSE;
?>
<?php 
    include_once 'style.php';
    include 'header.php';
    include_once 'database_util.php';
?>

<br/>
<div class="form">
    <form method="post" action="">
    帐号: <input type="text" class="login" name="user_name" required oninvalid="setCustomValidity('请填写账号');" oninput="setCustomValidity('')"/>
    密码: <input type="password" class="login" name="user_pwd" required oninvalid="setCustomValidity('请填写密码');" oninput="setCustomValidity('')"/>
    确认密码：<input type="password" class="login" name="user_pwd2" required oninvalid="setCustomValidity('请填写密码');" oninput="setCustomValidity('')"/>
    邮箱: <input type="text" class="login" name="user_email" required oninvalid="setCustomValidity('请填写邮箱');" oninput="setCustomValidity('')"/>
    昵称: <input type="text" class="login" name="user_nickname"/>
    <input type="submit" class="login" value="注册"/>
    <input type="hidden" name="call" value="12"/>
    </form>

<?php
    if(isset($_POST['call']) and $_POST['call']=="12")
    {
        if($_POST['user_pwd'] != $_POST['user_pwd2']) 
            die("两次输入的密码不同！");
        $conn = connect_db('localhost', 'web_user', '');

        $uid = query_one($conn,'user_id','sakura.user_info',
                'user_name','"'.$_POST['user_name'].'"');
        if($uid != NULL) die("注册失败：账号已存在！");
        $uid = query_one($conn,'user_email','sakura.user_info',
                'user_email','"'.$_POST['user_email'].'"');
        if($uid != NULL) die("注册失败：该邮箱已使用！");
        
        if($_POST['user_nickname'] == '') $_POST['user_nickname'] = '萝卜';
        $perm = '1';
        execute_sql($conn, "insert into sakura.user_info "
                . "(user_name,user_pwd,user_email,user_nickname,user_permission) "
                . "values ('".
                $_POST['user_name']."',PASSWORD('".
                $_POST['user_pwd']."'),'".
                $_POST['user_email']."','".$_POST['user_nickname']."',"
                .$perm.")");
        $_SESSION['user_name'] = $_POST['user_name'];
        echo "注册成功！";
        echo '<form method="post" action="/sign_in.php">
                <input type="submit" value="点此跳转至登录页面"/>
                <input type="hidden" name="user_name" value="'.$_POST['user_name'].'"/>
                <input type="hidden" name="user_pwd" value="'.$_POST['user_pwd'].'"/>
                </form>';
    }
?>

</div>
</body>
</html>