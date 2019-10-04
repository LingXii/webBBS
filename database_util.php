<?php
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


function f($conn, $sql)
{
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />语句执行错误：".mysqli_error($conn));
}

?>