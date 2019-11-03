<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura - 帖子管理</title>
</head>

<body>
<?php 
    include_once 'style.php';
    include_once 'database_util.php';
?>
<?php 
    if(!isset($_GET['bid'])) die("拒绝访问！");
    if($_GET['bid']==1) header('Location: board_manage.php');
    $str_bid = strval($_GET['bid']);
    $boardname = query_one($conn,'board_name','sakura.board',
            'board_id',$str_bid);
    if($boardname == NULL) die("版面不存在！");
    $title="Sakura 版面管理";
    $show_buttons = FALSE;
?>
<?php 
    include 'header.php';
?>  
<?php 
    $str_uid = strval($_SESSION['uid']);   
    if(!find($conn,'uid','sakura.manage','bid','1',$_SESSION['uid']) && 
        !find($conn,'uid','sakura.manage','bid',$str_bid,$_SESSION['uid']))
        die("访问错误：权限不足");
    $nickname = query_one($conn,'user_nickname','sakura.user_info',
            'user_id',$str_uid);
    echo "当前操作者：".$nickname;
?>

<br/>
<div class="form">
    <form method="post" action="">
    版面名: <input type="text" name="board_name" required oninvalid="setCustomValidity('不可为空');" oninput="setCustomValidity('')"/>
    <input type="submit" value="更改版面名"/>
    <input type="hidden" name="call" value="25"/>
    </form>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="25")
        {
            execute_sql($conn, "UPDATE sakura.board SET board_name = '".$_POST['pid']."' WHERE board_id = ".$_GET['bid']);
        }
        else if($_POST['call']=="26")
        {
            $time = strval(time());
            $state = '4';
            if(isset($_POST['replyable'])) $state = '5';
            $sql = "insert into sakura.posts (post_title,post_bid,post_uid,post_createtime,post_updatetime,"
                    ."post_content,post_state) value ('".$_POST['title']."',".$str_bid.",".$_SESSION['uid']
                    .",".$time.",".$time.",'".$_POST['content']."',".$state.")";
            execute_sql($conn, $sql);
        }
        else if($_POST['call']=="27")
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
        else if($_POST['call']=="28")
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
        else if($_POST['call']=="29")
        {
            $old_state = query_one($conn,'post_state','sakura.posts','post_id',$_POST['pid']);
            $new_state = 1;
            if($old_state != 2) $new_state = 2;
            execute_sql($conn, "UPDATE sakura.posts SET post_state = ".$new_state." WHERE post_id = ".$_POST['pid']);
        }
        else if($_POST['call']=="30")
        {
            execute_sql($conn, "DELETE FROM sakura.posts WHERE post_id = ".$_POST['pid']);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: post_manage.php?bid='.$str_bid);
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
            .'<input type="hidden" name="call" value="27"/>'
            .'<input type="hidden" name="pid" value="'.$str_pid.'"/>'
            .'</form>';
    $reply_tip = "设为不可回复";
    if($row[7] == 3 || $row[7] == 5) $reply_tip = "设为可回复";
    $reply_flip_form = '<form method="post" action="">'
            .'<input type="submit" value="'.$reply_tip.'"/>'
            .'<input type="hidden" name="call" value="28"/>'
            .'<input type="hidden" name="pid" value="'.$str_pid.'"/>'
            .'</form>';
    $lock_tip = "锁定";
    if($row[7] == 2) $lock_tip = "解除锁定";
    $lock_flip_form = '<form method="post" action="">'
            .'<input type="submit" value="'.$lock_tip.'"/>'
            .'<input type="hidden" name="call" value="29"/>'
            .'<input type="hidden" name="pid" value="'.$str_pid.'"/>'
            .'</form>';
    $delete_post_form = '<form method="post" action="">'
            .'<input type="submit" value="删除"/>'
            .'<input type="hidden" name="call" value="30"/>'
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
    <input type="submit" value="发布版面公告"/>
    <label><input type="checkbox" checked="checked" name="replyable" value="0" >不可回复</label>
    <input type="hidden" name="call" value="26"/>
    </form>
</div>

</body>
</html>