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
    // 全局变量
    $title = "Sakura - 开发页面";
    $show_buttons = FALSE;
?>
<?php 
    include_once 'database_util.php'; 
    include 'header.php'; 
?>
<p>
<?php echo "已连接数据库服务器：".$_SESSION['dbhost']." 管理员：".$_SESSION['dbuser'];?>
</p>
<?php 
    $conn = connect_db($_SESSION['dbhost'], $_SESSION['dbuser'], $_SESSION['dbpass']);
?>
<p><font color="red">创建数据库或表时，数据库名和表名不要含有中文、空格；不要乱删库！</font></p>

<form method="post" action="">
<input type="submit" value="查看现有数据库" />
<input type="hidden" name="call" value="3" />
</form>

<form method="post" action="">
<input type="text" name="dbname" value="" placeholder="数据库名">
<input type="submit" value="新建数据库" />
<input type="hidden" name="call" value="4" />
</form>

<form method="post" action="">
<input type="text" name="dbname" value="" placeholder="数据库名">
<input type="submit" value="删除数据库" />
<input type="hidden" name="call" value="5" />
</form>

<form method="post" action="">
<input type="text" name="dbname" value="" placeholder="数据库名">
<input type="submit" value="进入数据库" />
<input type="hidden" name="call" value="6" />
</form>

<br />
<form method="post" action="">
<input type="submit" value="查看现有数据表" />
<input type="hidden" name="call" value="7" />
</form>

<form method="post" action="">
<input type="text" name="tbname" value="" placeholder="数据表名">
<input type="submit" value="查询全部内容" />
<input type="hidden" name="call" value="8" />
</form>

<form method="post" action="">
<input type="text" name="tbname" value="" placeholder="数据表名">
<input type="submit" value="查询数据表属性" />
<input type="hidden" name="call" value="9" />
</form>

<form method="post" action="">
<input type="text" name="sql" value="" placeholder="SQL">
<input type="submit" value="执行SQL语句" />
<input type="hidden" name="call" value="10" />
</form>

<form method="post" action="">
<input type="submit" value="创建网站数据库系统" />
<input type="hidden" name="call" value="11" />
</form>

<form method="post" action="">
<input type="submit" value="创建网站数据库用户" />
<input type="hidden" name="call" value="14" />
</form>

<form method="post" action="">
<input type="submit" value="一刀999" />
<input type="hidden" name="call" value="999" />
</form>

<?php
    // 每个按钮调用相应的函数(请把函数写到util文件里面)
    if(isset($_POST['call']))
    {
        if($_POST['call']=="3") show_dbs($conn);
        else if($_POST['call']=="4") create_db($conn,$_POST['dbname']);
        else if($_POST['call']=="5") delete_db($conn,$_POST['dbname']);
        else if($_POST['call']=="6") select_db($conn,$_POST['dbname']);
        else if($_POST['call']=="7") show_tbs($conn);
        else if($_POST['call']=="8") show_tb($conn,$_POST['tbname']);
        else if($_POST['call']=="9") show_tb_attr($conn,$_POST['tbname']);
        else if($_POST['call']=="10") execute_sql_debug($conn,$_POST['sql']);
        else if($_POST['call']=="11") build_web_database($conn);
        else if($_POST['call']=="14") build_database_user($conn);
        else if($_POST['call']=='999') init($conn);
    }
?>

</body>
</html>