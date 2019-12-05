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
    include_once 'database_util.php';
?>
<?php 
    if(!isset($_GET['bid'])) die("拒绝访问！");
    if($_GET['bid']==1) header('Location: board_manage.php');
    $bid = $_GET['bid'];
    $boardname = query_one($conn,'board_name','sakura.board',
            'board_id',$bid);
    if($boardname == NULL) die("版面不存在！");
    $title="Sakura 版面管理";
    $show_buttons = TRUE;
    if(isset($_GET['page'])) $page = $_GET['page'];
    else $page = 1;
    $post_per_page = 30;
?>

<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="25")
        {
            execute_sql($conn, "UPDATE sakura.board SET board_name = '".$_POST['pid']."' WHERE board_id = ".$_GET['bid']);
        }
        else if($_POST['call']=="27")
        {
            $new_state = 1;
            execute_sql($conn, "UPDATE sakura.reply SET reply_state = ".$new_state." WHERE reply_id = ".$_POST['rid']);
        }
        else if($_POST['call']=="28")
        {
            execute_sql($conn, "DELETE FROM sakura.reply WHERE reply_id = ".$_POST['rid']);
        }
        else if($_POST['call']=="29")
        {
            $new_state = 1;
            execute_sql($conn, "UPDATE sakura.posts SET post_state = ".$new_state." WHERE post_id = ".$_POST['pid']);
        }
        else if($_POST['call']=="30")
        {
            execute_sql($conn, "DELETE FROM sakura.posts WHERE post_id = ".$_POST['pid']);
        }
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: post_manage.php?bid='.$bid);
    }
    
    include_once 'style.php';
    include 'header.php';  
?>
    
<br/>
<div class="editor">
<?php 
    $str_uid = strval($_SESSION['uid']);   
    if(!check_board_manager($conn,$bid))
        die("访问错误：权限不足");
    $nickname = query_one($conn,'user_nickname','sakura.user_info',
            'user_id',$str_uid);
    echo '<p><font color="red">当前操作者：'.$nickname.'</font></p>';
?>

    <form method="post" action="">
    版面名: <input type="text" class="button" name="board_name" required oninvalid="setCustomValidity('不可为空');" oninput="setCustomValidity('')"/>
    <input type="submit" class="button" value="更改版面名"/>
    <input type="hidden" name="call" value="25"/>
    </form>
</div>

<br />
<div class="middle_big">
<?php
$post_num = query_num($conn,'sakura.posts','post_bid = '.$bid.' and post_state = 2');
$reply_num = query_num($conn,'sakura.reply INNER JOIN sakura.posts ON reply_pid = post_id ','post_bid = '.$bid.' and reply_state = 2');
$page_num = ceil(($post_num+$reply_num)/$post_per_page);
if($page_num <= 0) $page_num = 1;

echo '<table border="1" id="posts"><tr>';
echo '<th><b>id</b></th>';
echo '<th><b>主题帖</b></th>';
echo '<th><b>发布内容</b></th>';
echo '<th><b>用户</b></th>';
echo '<th><b>发布时间</b></th>';
echo '<th><b>操作</b></th>';
echo '</tr>';
$row_cnt = 0;

$sql = "SELECT * FROM sakura.posts WHERE post_bid = ".$bid." and post_state = 2 ORDER BY post_updatetime DESC";
$post_val = mysqli_query($conn,$sql);
if(! $post_val) die("查询数据库失败：".mysqli_error($conn));
while($row = mysqli_fetch_array($post_val))
{
    $row_cnt += 1;
    if($row_cnt <= ($page-1)*$post_per_page) continue;
    if($row_cnt > $page*$post_per_page) break;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="9%">pid='.$row[0].'</td>';
    echo '<td width="20%"><a href="/post_reader.php?pid='.$row[0].'">'.$row[1].'</a></td>';
    echo '<td width="40%">'.$row[6].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[3]);
    echo '<td width="10%"><a href="/user_space.php?uid='.$row[3].'">'.$user_nickname.'</a></td>';
    $createtime = date('Y-n-j H:i:s',$row[4]);
    echo '<td width="13%">'.$createtime.'</td>';

    $lock_tip = "解除锁定";
    $lock_flip_form = '<form method="post" action="">'
            .'<input type="submit" value="'.$lock_tip.'" class="posts"/>'
            .'<input type="hidden" name="call" value="29"/>'
            .'<input type="hidden" name="pid" value="'.$row[0].'"/>'
            .'</form>';
    $delete_post_form = '<form method="post" action="">'
            .'<input type="submit" value="删除" class="posts"/>'
            .'<input type="hidden" name="call" value="30"/>'
            .'<input type="hidden" name="pid" value="'.$row[0].'"/>'
            .'</form>';
    echo '<td width="8%">'.$lock_flip_form.$delete_post_form.'</td>';
    echo '</tr>';      
}

$sql = "SELECT * FROM sakura.reply INNER JOIN sakura.posts ON reply_pid = post_id WHERE post_bid = ".$bid." and reply_state = 2 ORDER BY reply_createtime DESC";
$reply_val = mysqli_query($conn,$sql);
if(! $reply_val) die("查询数据库失败：".mysqli_error($conn));
while($row = mysqli_fetch_array($reply_val))
{
    $row_cnt += 1;
    if($row_cnt <= ($page-1)*$post_per_page) continue;
    if($row_cnt > $page*$post_per_page) break;
    if($row_cnt%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="9%">rid='.$row[0].'</td>';
    $pid = $row[2];
    $post_title = query_one($conn,'post_title','sakura.posts','post_id',$pid);
    echo '<td width="20%"><a href="/post_reader.php?pid='.$pid.'">'.$post_title.'</a></td>';
    echo '<td width="40%">'.$row[4].'</td>';
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[1]);
    echo '<td width="10%"><a href="/user_space.php?uid='.$row[1].'">'.$user_nickname.'</a></td>';
    $createtime = date('Y-n-j H:i:s',$row[3]);
    echo '<td width="13%">'.$createtime.'</td>';

    $lock_tip = "解除锁定";
    $lock_flip_form = '<form method="post" action="">'
            .'<input type="submit" value="'.$lock_tip.'" class="posts"/>'
            .'<input type="hidden" name="call" value="27"/>'
            .'<input type="hidden" name="rid" value="'.$row[0].'"/>'
            .'</form>';
    $delete_post_form = '<form method="post" action="">'
            .'<input type="submit" value="删除" class="posts"/>'
            .'<input type="hidden" name="call" value="28"/>'
            .'<input type="hidden" name="rrid" value="'.$row[0].'"/>'
            .'</form>';
    echo '<td width="8%">'.$lock_flip_form.$delete_post_form.'</td>';
    echo '</tr>';      
}

echo '</table>';
?>
</div> 

<br/>
<div>
<?php
if($page==1)
        echo '<a href="/post_manage.php?bid='.$bid.'&page=1" class="npage_btn" style="margin-left:15px;">第一页</a>';
else
    echo '<a href="/post_manage.php?bid='.$bid.'&page=1" class="page_btn" style="margin-left:15px;">第一页</a>';

if($page==1)
{
    echo '<a href="/post_manage.php?bid='.$bid.'&page=1" class="npage_btn">1</a>';
    if($page_num>1)
        echo '<a href="/post_manage.php?bid='.$bid.'&page=2" class="page_btn">2</a>';
}
else if($page==$page_num)
{
    if($page>1)
        echo '<a href="/post_manage.php?bid='.$bid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/post_manage.php?bid='.$bid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
}
else
{
    echo '<a href="/post_manage.php?bid='.$bid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/post_manage.php?bid='.$bid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
    echo '<a href="/post_manage.php?bid='.$bid.'&page='.($page+1).'" class="page_btn">'.($page+1).'</a>';
}

if($page==$page_num)
    echo '<a href="/post_manage.php?bid='.$bid.'&page='.$page_num.'" class="npage_btn">最后一页</a>';
else
    echo '<a href="/post_manage.php?bid='.$bid.'&page='.$page_num.'" class="page_btn">最后一页</a>';
?>
</div>

</body>
</html>