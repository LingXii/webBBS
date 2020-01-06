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
    $title="Sakura";
    $show_buttons = TRUE;
?>
<?php 
    include_once 'database_util.php';
    include_once 'image_util.php';
?>

<?php
    if(isset($_POST['call']))
    {
        
    }
    include_once 'style.php';
    include 'header.php';  
?>

<div class="form" style="margin-top: 50px;">
<form method="post" action="">
    原密码：<input type="password" name="passwd_old" class="login" required oninvalid=setCustomValidity("不可为空") oninput=setCustomValidity('')>
    新密码：<input type="password" name="passwd_new1" class="login" required oninvalid=setCustomValidity("不可为空") oninput=setCustomValidity('')>
    确认密码：<input type="password" name="passwd_new2" class="login" required oninvalid=setCustomValidity("不可为空") oninput=setCustomValidity('')>
    <input type="submit" value="确认" class="login">
    <input type="hidden" name="call" value="change">
</form>


<?php
    if (isset($_POST['call']) and $_POST['call']=="change")
    {
        $sql = "SELECT * from sakura.user_info
                where user_id = ".$_SESSION["uid"].
                " AND user_pwd = PASSWORD('".$_POST['passwd_old']."')" ;
        $retval = execute_sql($conn, $sql);
        if (!mysqli_fetch_array($retval))
            echo '<p style="text-align:center;"><font color="red">旧密码对上不能</font></p>';
        else if (!preg_match("/^[a-zA-Z0-9_]{6,32}$/", $_POST['passwd_new1']))
            echo '<p style="text-align:center;"><font color="red">密码请使用6-32字符的英文字母、数字、下划线！</font></p>';
        else if ($_POST['passwd_new1'] != $_POST['passwd_new2'])
            echo '<p style="text-align:center;"><font color="red">两次输入的密码不同！</font></p>';
        else
        {
            $passwd_new = $_POST['passwd_new1'];
            $sql = "UPDATE sakura.user_info
                    set user_pwd = PASSWORD('$passwd_new')
                    where user_id = ".$_SESSION['uid'];
            execute_sql($conn, $sql);
            echo '修改成功';
            echo "<script language='javascript' type='text/javascript'>
                    window.location.href='/temp_page.php'
                    </script>";
        }
    }
?>

</div>

</body>
</html>