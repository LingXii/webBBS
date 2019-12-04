<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura - 版面管理</title>
</head>

<body>
<?php 
    $title="Sakura 论坛管理";
    $show_buttons = TRUE;
?>
<?php 
    include_once 'database_util.php';
?>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="16")
        {
            execute_sql($conn, "insert into sakura.board (board_name) value ('"
                    .$_POST['board_name']."')");
        }
        else if($_POST['call']=="17")
        {
            execute_sql($conn, "DELETE FROM sakura.manage WHERE bid = "
                    .$_POST['bid']." AND uid = ".$_POST['uid']);
        }
        else if($_POST['call']=="18")
        {
            if(! find($conn,'user_id','sakura.user_info','user_id',$_POST['uid'],$_POST['uid']))
                die("不存在的用户");
            execute_sql($conn, "insert into sakura.manage (bid,uid) value ("
                    .$_POST['bid'].",".$_POST['uid'].")");
        }
        else if($_POST['call']=="19")
        {
            if($_POST['bid'] == '1') die("不允许删除该版面！");
            execute_sql($conn, "DELETE FROM sakura.board WHERE board_id = ".$_POST['bid']);
        }
        else if($_POST['call']=="20")
        {
            $time = strval(time());
            $state = '4';
            if(isset($_POST['replyable'])) $state = '5';
            $sql = "insert into sakura.posts (post_title,post_bid,post_uid,post_createtime,post_updatetime,"
                    ."post_content,post_state) value ('".$_POST['title']."',1,".$_SESSION['uid']
                    .",".$time.",".$time.",'".$_POST['content']."',".$state.")";
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="21")
        {
            $old_state = query_one($conn,'post_state','sakura.posts','post_id',$_POST['pid']);
            $new_state = 1;
            switch($old_state)
            {
                case 1:$new_state = 4;break;
                case 3:$new_state = 5;break;
                case 4:$new_state = 1;break;
                case 5:$new_state = 3;break;
            }
            execute_sql($conn, "UPDATE sakura.posts SET post_state = ".$new_state." WHERE post_id = ".$_POST['pid']);
        }
        else if($_POST['call']=="22")
        {
            $old_state = query_one($conn,'post_state','sakura.posts','post_id',$_POST['pid']);
            $new_state = 1;
            switch($old_state)
            {
                case 1:$new_state = 3;break;
                case 3:$new_state = 1;break;
                case 4:$new_state = 5;break;
                case 5:$new_state = 4;break;
            }
            execute_sql($conn, "UPDATE sakura.posts SET post_state = ".$new_state." WHERE post_id = ".$_POST['pid']);
        }
        else if($_POST['call']=="23")
        {
            $old_state = query_one($conn,'post_state','sakura.posts','post_id',$_POST['pid']);
            $new_state = 1;
            if($old_state != 2) $new_state = 2;
            execute_sql($conn, "UPDATE sakura.posts SET post_state = ".$new_state." WHERE post_id = ".$_POST['pid']);
        }
        else if($_POST['call']=="24")
        {
            execute_sql($conn, "DELETE FROM sakura.posts WHERE post_id = ".$_POST['pid']);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: board_manage.php');
    }
    
    include_once 'style.php';
    include 'header.php';
?>

<br/>
<div class="editor">
<?php 
    $str_uid = strval($_SESSION['uid']);   
    if(! find($conn,'uid','sakura.manage','bid','1',$_SESSION['uid']))
        die("访问错误：权限不足");
    $nickname = query_one($conn,'user_nickname','sakura.user_info',
            'user_id',$str_uid);
    echo '<p><font color="red">当前操作者：'.$nickname.'</font></p>';
?>  

    <form method="post" action="">
    版面名: <input type="text" class="button" name="board_name" required oninvalid="setCustomValidity('不可为空');" oninput="setCustomValidity('')"/>
    <input type="submit" class="button" value="创建新版面"/>
    <input type="hidden" name="call" value="16"/>
    </form>
</div>

<br />
<div class="middle">
<?php
$sql = "SELECT * FROM sakura.board ORDER BY board_id ASC";
$board_val = mysqli_query($conn,$sql);
if(! $board_val)
die("查询数据库失败：".mysqli_error($conn));

echo '<table border="1" id="posts"><tr>';
echo '<th><b>版面id</b></th>';
echo '<th><b>版面名</b></th>';
echo '<th><b>版面管理员</b></th>';
echo '</tr>';
$row_cnt = 0;
while($row = mysqli_fetch_array($board_val))
{
    $str_bid = strval($row[0]);
    $row_cnt += 1;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="15%">'.$row[0].'</td>';
    echo '<td width="25%"><a href="/post_manage.php?bid='.$str_bid.'">'.$row[1].'</a></td>';
    
    $sql = "SELECT uid FROM sakura.manage WHERE bid = ".strval($row[0]);
    $uid_val = mysqli_query($conn,$sql);
    $managers = '<table border="0" id="people">';
    $last_uid = '0';
    while($uid_row = mysqli_fetch_array($uid_val))
    {
        $str_uid = strval($uid_row[0]);
        if($str_uid == $last_uid) break;
        $last_uid = $str_uid;
        $nickname = query_one($conn,'user_nickname','sakura.user_info',
                'user_id',$str_uid);
        $delete_maneger_form = '<form method="post" action="">'
                .'<a href="/user_space.php?uid='.$str_uid.'">'.$nickname.'</a>'
                .'<input type="submit" value="撤职"/>'
                .'<input type="hidden" name="call" value="17"/>'
                .'<input type="hidden" name="uid" value="'.$str_uid.'"/>'
                .'<input type="hidden" name="bid" value="'.$str_bid.'"/>'
                .'</form>';
        $managers = $managers.'<tr><td>'.$delete_maneger_form.'</td></tr>';
    }
    
    $new_maneger_form = '<form method="post" action="">'
        .'<input type="text" placeholder="uid" name="uid" required oninvalid="setCustomValidity(\'不可为空\');" oninput="setCustomValidity(\'\')"/>'
        .'<input type="submit" value="任命版面管理员"/>'
        .'<input type="hidden" name="call" value="18"/>'
        .'<input type="hidden" name="bid" value="'.$str_bid.'"/>'
        .'</form>';
    
    $managers = $managers.'<tr><td>'.$new_maneger_form.'</td></tr>';
    $managers = $managers.'</table>';
    
    echo '<td  width="60%">'.$managers.'</td>';        
}
echo '</table>';
?>
</div> 

</body>
</html>