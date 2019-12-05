<?php
session_start();
$_SESSION['dbhost'] = "";
$_SESSION['dbuser'] = "";
$_SESSION['dbpass'] = "";
$_SESSION['use_db'] = "";
?>

<?php
// 全局变量
$title = "Sakura - 开发页面";
$show_buttons = FALSE;
?>

<?php
    function page_reset()
    {
        array_splice($_POST, 0, count($_POST));
    }
?>
    
<?php
    function db_connect($h,$u,$p)
    {
        $conn = mysqli_connect($h,$u,$p);
        if(! $conn) die("连接失败：".mysqli_connect_error());
        $_SESSION['dbhost'] = $h;
        $_SESSION['dbuser'] = $u;
        $_SESSION['dbpass'] = $p;
        header('Location: debug_database.php');
    }
?>
<?php
// 每个按钮调用相应的函数
if(isset($_POST['call']))
{
    if($_POST['call']=="1") page_reset();
    else if($_POST['call']=="2") db_connect($_POST['dbhost'],
        $_POST['dbuser'],$_POST['dbpass']);
    array_splice($_POST, 0, count($_POST));
}
?>
    
<?php include_once 'header.php'; ?>

<form method="post" action="">
<input type="submit" value="重置页面" />
<input type="hidden" name="call" value="1" />
</form>

<form method="post" action="">
数据库host: <input type="text" name="dbhost" value="localhost">
用户名: <input type="text" name="dbuser" value="root">
密码： <input type="password" name="dbpass" value="">
<input type="submit" value="连接数据库" />
<input type="hidden" name="call" value="2" />
</form>

</body>
</html>