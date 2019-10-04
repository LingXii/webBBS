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
    注册成功!<br>
</body>

<form method="post" action="">
你现在可以选择：<br>
<input type="submit" value="注销账号"/>
<input type="hidden" name="call" value="1"/>
</form>

<?php
    if(isset($_POST['call']) and $_POST['call']=="1")
    {
        $conn = mysqli_connect('localhost', 'root', '');
        if(!$conn) die("连接失败：".mysqli_connect_error());
        f($conn, 'delete from sakura.userinfo
                    where user_name = '.$_SESSION['user_name']);
        header('Location: index.php');
    }
?>

</html>