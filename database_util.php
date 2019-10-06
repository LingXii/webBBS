<?php
function connect_db($host,$user_name,$password)
{
    $conn = mysqli_connect($host, $user_name, $password);
    if(!$conn) die("数据库服务器发生错误：".mysqli_connect_error());
    mysqli_set_charset($conn,'utf8');
    return $conn;
}

function create_db($conn, $name)
{
    $sql = "CREATE DATABASE ".$name;
    if(! mysqli_query($conn,$sql))
        die("创建数据库失败：".mysqli_error($conn));
    echo "数据库".$name."创建成功";
}

function delete_db($conn, $name)
{
    $forbid = array("mysql","sys","information_schema","performance_schema"); // 禁止删除的数据库
    foreach($forbid as $f) if(strcasecmp($name,$f)==0) die("禁止删除此数据库！");
    $sql = "DROP DATABASE ".$name;
    if(! mysqli_query($conn,$sql))
        die("删除数据库失败：".mysqli_error($conn));
    echo "数据库".$name."已删除";
}

function select_db($conn, $name)
{
    if(! mysqli_select_db($conn,$name))
        die("进入数据库失败：".mysqli_error($conn));
    echo "当前数据库：".$name;
    $_SESSION['use_db'] = $name;
}

function echo_table($val)
{
    echo '<br />';  
    $first_in = 1;
    $n = 0;
    while($row = mysqli_fetch_array($val))
    {
        if($first_in)
        {
            $keys = array_keys($row);
            $n = count($keys);      
            if($n == 0) break;
            echo '<table border="1"><tr>';
            for($i=1;$i<$n;$i+=2) echo '<td><b>'.$keys[$i].'</b></td>';
            echo '</tr>';
            $first_in = 0;
        }
        echo '<tr>';
        for($i=1;$i<$n;$i+=2) echo '<td>'.$row[$keys[$i]].'</td>';
        echo '</tr>';      
    }
    if($n == 0) echo "No content.";
    else echo '</table>';
}

function show_dbs($conn)
{
    $sql = "SHOW DATABASES";
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询数据库失败：".mysqli_error($conn));
    echo_table($retval);
}

function show_tbs($conn)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SHOW TABLES";
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询数据表失败：".mysqli_error($conn));
    echo_table($retval);
}

function show_tb($conn,$name)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SELECT * FROM ".$name;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询失败：".mysqli_error($conn));
    echo "→".$name;
    echo_table($retval);
}

function show_tb_attr($conn,$name)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SHOW COLUMNS FROM ".$name;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询失败：".mysqli_error($conn));
    echo "→".$name;
    echo_table($retval);
}

function execute_sql($conn,$sql)
{
    select_db($conn, $_SESSION['use_db']);
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />语句执行错误：".mysqli_error($conn));  
    if(strcasecmp(substr($sql,0,6),"SELECT")==0)
    {
        $begin = stripos($sql,"FROM ") + 5;
        $end = stripos($sql," ",$begin);
        if(!$end) $end = strlen($sql);
        $name = substr($sql,$begin,$end-$begin);
        echo "→".$name;
        echo_table($retval);
    }
    echo "<br />执行语句成功：".$sql;
}

function qurey_one($conn,$select,$from,$where_key,$where_value)
{   //调用此函数须确保查询的结果唯一
    $sql = 'select '.$select.' from '.$from.' where '.$where_key.'='.$where_value;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

function execute_sql_outside($conn, $sql)
{
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />语句执行错误：".mysqli_error($conn));
    echo "<br />执行语句成功：".$sql;
}

function build_web_database($conn)
{
    // TODO: 主码，外码，check，自增等等
    
    // 用户表：uid，账号，密码，邮箱,昵称，头像地址，权限(0游客，1用户，2管理员，3大老板)，
    // 管理的版面(bid,多个版面用'|'分隔)
    $table = 'user_info(
            user_id int auto_increment,
            user_name varchar(32) unique,
            user_pwd blob(128),
            user_email varchar(32) unique,
            user_nickname varchar(32),
            user_headpic_url varchar(256),                
            user_permission int,
            user_admin_board varchar(512),
            primary key (user_id))';
    execute_sql_outside($conn, 'create table if not exists sakura.'.$table);
    $sql = 'alter table sakura.user_info CONVERT TO CHARACTER SET utf8';
    execute_sql_outside($conn, $sql);
    
    // 版面表：bid（1为总版面），名称，管理员(uid,多个管理员用'|'分隔)
    $table = 'board(
            board_id int auto_increment,
            board_name varchar(16) unique,
            board_admin varchar(512),
            primary key (board_id))';
    execute_sql_outside($conn, 'create table if not exists sakura.'.$table);
    $sql = 'alter table sakura.board CONVERT TO CHARACTER SET utf8';
    execute_sql_outside($conn, $sql);
    
    // 帖子表：pid，标题，所属版面，发帖用户，创建时间，更新时间，
    // 内容(检测越界，内容过多则用文件储存，数据库放文件路径)，状态(1正常，2违规，3不可回复)
    $table = 'posts(
            post_id int auto_increment,
            post_title varchar(128),
            post_bid int,
            post_uid int,
            post_createtime datetime,
            post_updatetime datetime,
            post_content varchar(16384),
            post_state int,
            primary key (post_id))';
    execute_sql_outside($conn, 'create table if not exists sakura.'.$table);
    $sql = 'alter table sakura.posts CONVERT TO CHARACTER SET utf8';
    execute_sql_outside($conn, $sql);
                
    // 回复表：rid，发回复的用户，所属帖子，回复时间，
    // 内容(检测越界，内容过多则用文件储存，数据库放文件路径)，状态(1正常，2违规)
    $table = 'reply(
            reply_id int auto_increment,
            reply_uid int,
            reply_pid int,
            reply_createtime datetime,
            reply_content varchar(16384), 
            reply_state int,
            primary key (reply_id))';
    execute_sql_outside($conn, 'create table if not exists sakura.'.$table);  
    $sql = 'alter table sakura.reply CONVERT TO CHARACTER SET utf8';
    execute_sql_outside($conn, $sql);
    
}

function check_usrpsw($conn,$name,$psw)
{   //检查用户账号密码是否正确，返回uid
    $sql = 'SELECT user_id FROM sakura.user_info WHERE user_name="'.$name.'"'
            . 'AND user_pwd=PASSWORD("'.$psw.'")';
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />发生错误：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

?>