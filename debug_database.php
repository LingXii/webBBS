<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>虾扯蛋</title>
</head>

<body>
<?php
// 全局变量
$title = "虾扯蛋 - 开发页面";
$conn = mysqli_connect($_SESSION['dbhost'],$_SESSION['dbuser'],$_SESSION['dbpass']);
?> 
<?php 
include 'database_util.php'; 
include 'header.php'; 
?>
 
<p><font color="red">创建数据库或表时，数据库名和表名不要含有中文、空格；不要乱删库！</font></p>
    
<?php 
if(! $conn) die("数据库服务器发生错误：".mysqli_connect_error());
mysqli_set_charset($conn,'utf8');
?>
<p>
<?php echo "已连接数据库服务器：".$_SESSION['dbhost']." 管理员：".$_SESSION['dbuser'];?>
</p>

<form method="post" action="">
<input type="submit" value="查看现有数据库" />
<input type="hidden" name="call" value="show_dbs" />
</form>

<form method="post" action="">
<input type="text" name="dbname" value="" placeholder="数据库名">
<input type="submit" value="新建数据库" />
<input type="hidden" name="call" value="create_db" />
</form>

<form method="post" action="">
<input type="text" name="dbname" value="" placeholder="数据库名">
<input type="submit" value="删除数据库" />
<input type="hidden" name="call" value="delete_db" />
</form>

<form method="post" action="">
<input type="text" name="dbname" value="" placeholder="数据库名">
<input type="submit" value="进入数据库" />
<input type="hidden" name="call" value="select_db" />
</form>

<br />
<form method="post" action="">
<input type="submit" value="查看现有数据表" />
<input type="hidden" name="call" value="show_tbs" />
</form>

<form method="post" action="">
<input type="text" name="tbname" value="" placeholder="数据表名">
<input type="submit" value="查询全部内容" />
<input type="hidden" name="call" value="show_tb" />
</form>

<form method="post" action="">
<input type="text" name="tbname" value="" placeholder="数据表名">
<input type="submit" value="查询数据表属性" />
<input type="hidden" name="call" value="show_tb_attr" />
</form>

<form method="post" action="">
<input type="text" name="sql" value="" placeholder="SQL">
<input type="submit" value="执行SQL语句" />
<input type="hidden" name="call" value="execute_sql" />
</form>

<?php
// 每个按钮调用相应的函数
if(isset($_POST['call']))
{
    if($_POST['call']=="show_dbs") show_dbs($conn);
    else if($_POST['call']=="create_db") create_db($conn,$_POST['dbname']);
    else if($_POST['call']=="delete_db") delete_db($conn,$_POST['dbname']);
    else if($_POST['call']=="select_db") select_db($conn,$_POST['dbname']);
    else if($_POST['call']=="show_tbs") show_tbs($conn);
    else if($_POST['call']=="show_tb") show_tb($conn,$_POST['tbname']);
    else if($_POST['call']=="show_tb_attr") show_tb_attr($conn,$_POST['tbname']);
    else if($_POST['call']=="execute_sql") execute_sql($conn,$_POST['sql']);
}
?>

</body>
</html>