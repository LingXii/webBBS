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
    include_once 'style.php';
    include_once 'database_util.php';
?>
<?php 
    $str_bid = 1;
    if(isset($_GET['bid'])) $str_bid = strval($_GET['bid']);
    $conn = connect_db('localhost', 'web_user', '');
    $boardname = query_one($conn,'board_name','sakura.board',
            'board_id',$str_bid);
    if($boardname == NULL) die("版面不存在！"); 
    $title="Sakura 版面：".$boardname;
    if($str_bid == 1) $title="Sakura";
    $show_buttons = TRUE;
?>
<?php 
    include 'header.php';
?>  

<div>
    <?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="31")
        {
            $conn = connect_db('localhost', 'web_user', '');
            $time = strval(time());
            $state = '1';
            if(isset($_POST['replyable'])) $state = '3';
            $sql = "insert into sakura.posts (post_title,post_bid,post_uid,post_createtime,post_updatetime,"
                    ."post_content,post_state) value ('".$_POST['title']."',".$str_bid.",".$_SESSION['uid']
                    .",".$time.",".$time.",'".$_POST['content']."',".$state.")";
            execute_sql($conn, $sql);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: index.php?bid='.$str_bid);
    }
?>
</div>

<br />
<div>
<?php
$sql = "SELECT * FROM sakura.posts WHERE post_bid = ".$str_bid." ORDER BY post_id ASC";
$post_val = mysqli_query($conn,$sql);
if(! $post_val)
die("查询数据库失败：".mysqli_error($conn));

echo '<table border="1"><tr>';
echo '<td><b>帖子主题</b></td>';
echo '<td><b>发帖用户</b></td>';
echo '<td><b>发帖时间</b></td>';
echo '</tr>';
while($row = mysqli_fetch_array($post_val))
{
    $str_pid = strval($row[0]);
    echo '<tr>';
    echo '<td><a href="/post_reader.php?pid='.$str_pid.'">'.$row[1].'</a></td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',strval($row[3]));
    echo '<td><a href="/user_space.php?uid='.strval($row[3]).'">'.$user_nickname.'</a></td>';
    $createtime = date('Y-n-j H:i:s',$row[4]);
    echo '<td>'.$createtime.'</td>';
    echo '</tr>';      
}
echo '</table>';
?>
</div> 

<br />
<div>
<?php
if($str_bid == 1)
{
    $sql = "SELECT * FROM sakura.board ORDER BY board_id ASC";
    $board_val = mysqli_query($conn,$sql);
    if(! $board_val)
    die("查询数据库失败：".mysqli_error($conn));

    echo '<table border="1"><tr>';
    echo '<td><b>版面名</b></td>';
    echo '<td><b>版面管理员</b></td>';
    echo '</tr>';
    while($row = mysqli_fetch_array($board_val))
    {
        $str_bid_ = strval($row[0]);
        echo '<tr>';
        echo '<td><a href="/index.php?bid='.$str_bid_.'">'.$row[1].'</a></td>';

        $sql = "SELECT uid FROM sakura.manage WHERE bid = ".strval($row[0]);
        $uid_val = mysqli_query($conn,$sql);
        $managers = '<table border="0">';
        $last_uid = '0';
        while($uid_row = mysqli_fetch_array($uid_val))
        {
            $str_uid = strval($uid_row[0]);
            if($str_uid == $last_uid) break;
            $last_uid = $str_uid;
            $nickname = query_one($conn,'user_nickname','sakura.user_info',
                    'user_id',$str_uid);
            $nametext = '<a href="/user_space.php?uid='.$str_uid.'">'.$nickname.'</a>';
            $managers = $managers.'<tr><td>'.$nametext.'</td></tr>';
        }
        $managers = $managers.'</table>';

        echo '<td>'.$managers.'</td>';   
        echo '</tr>';      
    }
    echo '</table>';
}

?>
</div>

<br />
<div>
<?php
if($str_bid > 1 && $_SESSION['uid'] != 0)
{
    echo '<form method="post" action="">'.
        '帖子标题<input type="text" name="title" required oninvalid="setCustomValidity('."'不可为空'".');" oninput="setCustomValidity('."''".')"/>'.
        '<br />'.
        '<textarea cols="50" rows="10" name="content"></textarea>'.
        '<input type="submit" value="发帖"/>'.
        '<label><input type="checkbox" name="replyable" value="0" >不可回复</label>'.
        '<input type="hidden" name="call" value="31"/>'.
        '</form>';
}
?>
</div>

</body>
</html>