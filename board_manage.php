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
    $title="Sakura 论坛版面管理";
    $show_buttons = FALSE;
?>
<?php 
    include_once 'style.php';
    include 'header.php';
    include_once 'database_util.php';
?>
    
<?php 
    $str_uid = strval($_SESSION['uid']);   
    if(! find($conn,'uid','sakura.manage','bid','1',$_SESSION['uid']))
        die("访问错误：权限不足");
    $nickname = query_one($conn,'user_nickname','sakura.user_info',
            'user_id',$str_uid);
    echo "当前操作者：".$nickname;
?>

<br/>
<div class="form">
    <form method="post" action="">
    版面名: <input type="text" name="board_name" required oninvalid="setCustomValidity('不可为空');" oninput="setCustomValidity('')"/>
    <input type="submit" value="创建新版面"/>
    <input type="hidden" name="call" value="16"/>
    </form>

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
?>
</div>

<br />
<div>
<?php
$sql = "SELECT * FROM sakura.board ORDER BY board_id ASC";
$board_val = mysqli_query($conn,$sql);
if(! $board_val)
die("查询数据库失败：".mysqli_error($conn));

echo '<table border="1"><tr>';
echo '<td><b>版面id</b></td>';
echo '<td><b>版面名</b></td>';
echo '<td><b>版面管理员</b></td>';
echo '<td><b> </b></td>';
echo '</tr>';
while($row = mysqli_fetch_array($board_val))
{
    $str_bid = strval($row[0]);
    echo '<tr>';
    echo '<td>'.$row[0].'</td>';
    echo '<td><a href="/post_manage.php?bid='.$str_bid.'">'.$row[1].'</a></td>';
    
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
    
    echo '<td>'.$managers.'</td>';   
   
    $delete_board_form = '<form method="post" action="">'
            .'<input type="submit" value="删除版面"/>'
            .'<input type="hidden" name="call" value="19"/>'
            .'<input type="hidden" name="bid" value="'.$str_bid.'"/>'
            .'</form>';
    echo '<td>'.$delete_board_form.'</td>';
    echo '</tr>';      
}
echo '</table>';
?>
</div> 

<br />
<div>
<?php
$sql = "SELECT * FROM sakura.posts WHERE post_bid = 1 ORDER BY post_id ASC";
$post_val = mysqli_query($conn,$sql);
if(! $post_val)
die("查询数据库失败：".mysqli_error($conn));

echo '<table border="1"><tr>';
echo '<td><b>帖子id</b></td>';
echo '<td><b>帖子主题</b></td>';
echo '<td><b>发帖用户</b></td>';
echo '<td><b>发帖时间</b></td>';
echo '<td><b>帖子状态</b></td>';
echo '<td><b>操作</b></td>';
echo '</tr>';
while($row = mysqli_fetch_array($post_val))
{
    $str_pid = strval($row[0]);
    echo '<tr>';
    echo '<td>'.$str_pid.'</td>';
    echo '<td><a href="/post_reader.php?pid='.$str_pid.'">'.$row[1].'</a></td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',strval($row[3]));
    echo '<td><a href="/user_space.php?uid='.strval($row[3]).'">'.$user_nickname.'</a></td>';
    $createtime = date('Y-n-j H:i:s',$row[4]);
    echo '<td>'.$createtime.'</td>';
    $state = "正常";
    switch($row[7])
    {
        case 2: $state = "锁定";break;
        case 3: $state = "不可回复";break;
        case 4: $state = "置顶";break;
        case 5: $state = "置顶不可回复";break;
    }
    echo '<td>'.$state.'</td>';
    
    $top_tip = "置顶";
    if($row[7] >= 4) $top_tip = "取消置顶";
    $top_flip_form = '<form method="post" action="">'
            .'<input type="submit" value="'.$top_tip.'"/>'
            .'<input type="hidden" name="call" value="21"/>'
            .'<input type="hidden" name="pid" value="'.$str_pid.'"/>'
            .'</form>';
    $reply_tip = "设为不可回复";
    if($row[7] == 3 || $row[7] == 5) $reply_tip = "设为可回复";
    $reply_flip_form = '<form method="post" action="">'
            .'<input type="submit" value="'.$reply_tip.'"/>'
            .'<input type="hidden" name="call" value="22"/>'
            .'<input type="hidden" name="pid" value="'.$str_pid.'"/>'
            .'</form>';
    $lock_tip = "锁定";
    if($row[7] == 2) $lock_tip = "解除锁定";
    $lock_flip_form = '<form method="post" action="">'
            .'<input type="submit" value="'.$lock_tip.'"/>'
            .'<input type="hidden" name="call" value="23"/>'
            .'<input type="hidden" name="pid" value="'.$str_pid.'"/>'
            .'</form>';
    $delete_post_form = '<form method="post" action="">'
            .'<input type="submit" value="删除"/>'
            .'<input type="hidden" name="call" value="24"/>'
            .'<input type="hidden" name="pid" value="'.$str_pid.'"/>'
            .'</form>';
    if($row[7] == 2)
        echo '<td>'.$lock_flip_form.$delete_post_form.'</td>';
    else
        echo '<td>'.$top_flip_form.$reply_flip_form.$lock_flip_form.$delete_post_form.'</td>';
    echo '</tr>';      
}
echo '</table>';
?>
</div> 

<br />
<div>
    <form method="post" action="">
    公告标题<input type="text" name="title" required oninvalid="setCustomValidity('不可为空');" oninput="setCustomValidity('')"/>
    <br />
    <textarea cols="50" rows="10" name="content"></textarea>
    <input type="submit" value="发布论坛公告"/>
    <label><input type="checkbox" checked="checked" name="replyable" value="0" >不可回复</label>
    <input type="hidden" name="call" value="20"/>
    </form>
</div>

</body>
</html>