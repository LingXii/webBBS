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

<form method="post" action="">
<input type="submit" value="DEBUG" />
<input type="hidden" name="call" value="debug" />
</form>

<?php
    // 每个按钮调用相应的函数
    if(isset($_POST['call']))
    {
        if($_POST['call']=="show_dbs") show_dbs($conn);
        else if($_POST['call']=="debug")
        {
            # TODO: 主码，外码，check，自增等等
            $tables = array('UserInfo(
                            User_Name int,
                            User_Pwd varchar(16),
                            User_nickname varchar(16),
                            User_headpicurl varchar(255),
                            User_allmarks int,
                            User_roleid int,
                            primary key (User_Name))',
                        'BigBoard(
                            BiBo_id int,
                            BiBo_title varchar(16),
                            BiBo_admin varchar(255))',
                        'SmallBoard(
                            SmBo_id int,
                            SmBo_title varchar(16),
                            SmBo_admin varchar(255),
                            SmBo_BiBoid int)',
                        'Posts(
                            Post_id int,
                            Post_Title varchar(32),
                            Post_BiBoid int,
                            Post_SmBoid int,
                            Post_admin varchar(16),
                            Post_createtime datetime,
                            Post_updatetime datetime,
                            Post_content text,
                            Post_goodcount int,
                            Post_badcount int,
                            Post_reward int,
                            Post_score int,
                            Post_ispay int,
                            Post_islocked int)',
                        'Reply(
                            Reply_admin varchar(16),
                            Reply_postid int,
                            Reply_smboid int,
                            Reply_biboid int,
                            Reply_content text,
                            Reply_createtime datetime,
                            Reply_goodcount int,
                            Reply_badcount int,
                            Reply_score int,
                            Reply_id bigint)',
                        'PostCommend(
                            PoCo_id int,
                            PoCo_commendtype int,
                            PoCo_commendtime datetime,
                            PoCo_commendperson varchar(16),
                            PoCo_commendreason varchar(255))',
                        'DM_PostCommend(
                            Comm_type int,
                            Comm_summary varchar(255))',
                        'DM_UserRoles(
                            Role_id int,
                            Role_name varchar(16),
                            Role_permission varchar(16))',
                        'DM_Grade(
                            Grade_id int,
                            Grade_name varchar(16),
                            Grade_medalurl varchar(255))',
                        'ScoreLimit(
                            Score_username varchar(16),
                            Score_postmark int,
                            Score_replymark int,
                            Score_createtime datetime,
                            Score_updatetime datetime)');
            foreach($tables as $table)
            {
                f($conn, 'create table if not exists sakura.'.$table);
            }
            // f($conn, "insert into sakura.userinfo values (
            //             1261418785,
            //             '123456',
            //             'swzz',
            //             'www.aurl.com',
            //             0,
            //             0)");
        }
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