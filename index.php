<?php
    session_start();
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
?>
<?php 
    include 'header.php';
    include 'database_util.php';
?>
<p>SakuraSakura Ai Tai Yo ~</p>

<form method="post" action="">
米米号: <input type="text" name="user_name" value=1261418785>
密码: <input type="text" name="user_pwd" value=123456>
昵称: <input type="text" name="user_nickname" value=swzz>
头像地址: <input type="text" name="user_headpicurl" value=www.yigewangzhi.com>
总积分: <input type="text" name="user_allmarks" value=0>
角色: <input type="text" name="user_roleid" value=0>
<input type="submit" value="注册"/>
<input type="hidden" name="call" value="1"/>
</form>

<?php
    if(isset($_POST['call']) and $_POST['call']=="1")
    {
        $conn = mysqli_connect('localhost', 'root', '');
        if(!$conn) die("连接失败：".mysqli_connect_error());
        f($conn, "insert into sakura.userinfo values (".
                    $_POST['user_name'].",'".
                    $_POST['user_pwd']."','".
                    $_POST['user_nickname']."','".
                    $_POST['user_headpicurl']."',".
                    $_POST['user_allmarks'].",".
                    $_POST['user_roleid'].")");
        $_SESSION['user_name'] = $_POST['user_name'];
        header('Location: regis_ok.php');
    }
?>

</body>
</html>