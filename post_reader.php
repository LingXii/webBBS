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
    include_once 'database_util.php';
    include_once 'image_util.php';
?>
<?php 
    if(!isset($_GET['pid'])) die("拒绝访问！");
    $title="Sakura";
    $show_buttons = TRUE;
    $post_title = query_one($conn,'post_title','sakura.posts','post_id',$_GET['pid']);
    if($post_title == NULL) die("帖子不存在！");
    $pid = $_GET['pid'];
    $bid = query_one($conn,'post_bid','sakura.posts','post_id',$pid);
    if(isset($_GET['page'])) $page = $_GET['page'];
    else $page = 1;
    $reply_per_page = 30;
?>
<?php
    if(isset($_POST['call']))
    {
        if($_POST['call']=="49")
        {
            $new_state = 2;
            execute_sql($conn, "UPDATE sakura.posts SET post_state = ".$new_state." WHERE post_id = ".$_POST['pid']);
        }
        else if($_POST['call']=="50")
        {
            $new_state = 2;
            execute_sql($conn, "UPDATE sakura.reply SET reply_state = ".$new_state." WHERE reply_id = ".$_POST['rid']);
        }
        else if($_POST['call']=="60")
        {
            execute_sql($conn, "DELETE FROM sakura.posts WHERE post_id = ".$_POST['pid']);
        }
        else if($_POST['call']=="61")
        {
            execute_sql($conn, "UPDATE sakura.reply SET reply_content = '<font color=".'"red"'.">此内容已被发布者删除</font>' WHERE reply_id = ".$_POST['rid']);
        }
        else if($_POST['call']=="48")
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
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: post_reader.php?pid='.$pid);
    }
?>
<?php 
    include_once 'style.php';
    include 'header.php';    
?>
    
<br/>
<div>
<?php 
$post_state = query_one($conn,'post_state','sakura.posts','post_id',$pid);
if($post_state==2) 
{
    die("此贴内容因被锁定无法查看！");
}

$reply_num = query_one($conn,'count(*)','sakura.reply','reply_pid',$pid);
$page_num = ceil($reply_num/$reply_per_page);
if($page_num <= 0) $page_num = 1;

echo '<table border="1" id="reader"><tr>';
echo '<th><b></b></th>';
echo '<th><b></b></th>';
echo '<th><b>'.$post_title.'</b></th>';
echo '</tr>';

$layer = 0;
$post_uid = query_one($conn,'post_uid','sakura.posts','post_id',$pid);
$post_user = query_one($conn,'user_nickname','sakura.user_info','user_id',$post_uid);
$content = query_one($conn,'post_content','sakura.posts','post_id',$pid);
$post_timestamp = query_one($conn,'post_createtime','sakura.posts','post_id',$pid);
$post_time = date('Y-n-j H:i:s',$post_timestamp);
if($layer%2 == 0) echo '<tr class="posteven">';
else echo '<tr class="postodd">';
echo '<td width="5%">'.$layer.'</td>';
echo '<td width="20%"><a href="/user_space.php?uid='.$post_uid.'">';
show_headpic_60($post_uid);
echo '<br/>'.$post_user.'</a><br/>';
echo '<p>'.$post_time.'</p></td>';
echo '<td width="75%" valign="top">';

echo '<table border="0" id="list">';
echo '<tr><td height=24px>';
$lock_form = '<form method="post" action="">'
            .'<input type="submit" class="lock" value=""/>'
            .'<input type="hidden" name="call" value="49"/>'
            .'<input type="hidden" name="pid" value="'.$pid.'"/>'
            .'</form>';
$reply_tip = "禁止回复";
if($post_state == 3 || $post_state == 5) $reply_tip = "允许回复";
$reply_flip_form = '<form method="post" action="">'
        .'<input type="submit" value="'.$reply_tip.'" class="posts"/>'
        .'<input type="hidden" name="call" value="48"/>'
        .'<input type="hidden" name="pid" value="'.$pid.'"/>'
        .'</form>';
$delete_post_form = '<form method="post" action="">'
        .'<input type="submit" value="删除" class="posts"/>'
        .'<input type="hidden" name="call" value="60"/>'
        .'<input type="hidden" name="pid" value="'.$pid.'"/>'
        .'</form>';
if(check_board_manager($conn,$bid)) echo $lock_form;
if($_SESSION['uid']==$post_uid) echo $delete_post_form.$reply_flip_form;
echo '</td></tr>';
echo '<tr><td>'.$content.'</td></tr></table>';

echo '</td>';
echo '</tr>';  

$sql = "SELECT * FROM sakura.reply WHERE reply_pid = ".$pid." ORDER BY reply_createtime ASC";
$reply_val = mysqli_query($conn,$sql);
if(! $reply_val)
die("查询数据库失败：".mysqli_error($conn));
while($row = mysqli_fetch_array($reply_val))
{ 
    $layer += 1;
    if($layer <= ($page-1)*$reply_per_page) continue;
    if($layer > $page*$reply_per_page) break;
    if($layer%2 == 0) echo '<tr class="posteven">';
    else echo '<tr class="postodd">';
    echo '<td width="5%">'.$layer.'</td>';
    $reply_uid = $row[1];
    $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$reply_uid);
    $createtime = date('Y-n-j H:i:s',$row[3]);
    echo '<td width="20%"><a href="/user_space.php?uid='.$reply_uid.'">';
    show_headpic_60($reply_uid);
    echo '<br/>'.$user_nickname.'</a><br/>';
    echo '<p>'.$createtime.'</p></td>';
    echo '<td width="75%" valign="top">';

    echo '<table border="0" id="list">';
    echo '<tr><td height=24px>';
    $lock_form = '<form method="post" action="">'
                .'<input type="submit" class="lock" value=""/>'
                .'<input type="hidden" name="call" value="50"/>'
                .'<input type="hidden" name="rid" value="'.$row[0].'"/>'
                .'</form>';
    $delete_reply_form = '<form method="post" action="">'
        .'<input type="submit" value="删除" class="posts"/>'
        .'<input type="hidden" name="call" value="61"/>'
        .'<input type="hidden" name="rid" value="'.$row[0].'"/>'
        .'</form>';
    if(check_board_manager($conn,$bid)) echo $lock_form;
    if($_SESSION['uid']==$reply_uid) echo $delete_reply_form;
    echo '</td></tr>';
    if($row[5]==2) echo '<tr><td><font color="red">此回复已被锁定</font></td></tr></table>';
    else echo '<tr><td>'.$row[4].'</td></tr></table>';

    echo '</td>';
    echo '</tr>';      
}

echo '</table>';
?>
</div>

<br />
<div>
<?php
if(($post_state == 1 || $post_state == 4) && $_SESSION['uid'] != 0)
{
    echo '<a href="/editor.php?pid='.$pid.'" class="edit_btn">回帖</a>';
}

if($page==1)
        echo '<a href="/post_reader.php?pid='.$pid.'&page=1" class="npage_btn" style="margin-left:15px;">第一页</a>';
else
    echo '<a href="/post_reader.php?pid='.$pid.'&page=1" class="page_btn" style="margin-left:15px;">第一页</a>';

if($page==1)
{
    echo '<a href="/post_reader.php?pid='.$pid.'&page=1" class="npage_btn">1</a>';
    if($page_num>1)
        echo '<a href="/post_reader.php?pid='.$pid.'&page=2" class="page_btn">2</a>';
}
else if($page==$page_num)
{
    if($page>1)
        echo '<a href="/post_reader.php?pid='.$pid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/post_reader.php?pid='.$pid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
}
else
{
    echo '<a href="/post_reader.php?pid='.$pid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
    echo '<a href="/post_reader.php?pid='.$pid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
    echo '<a href="/post_reader.php?pid='.$pid.'&page='.($page+1).'" class="page_btn">'.($page+1).'</a>';
}

if($page==$page_num)
    echo '<a href="/post_reader.php?pid='.$pid.'&page='.$page_num.'" class="npage_btn">最后一页</a>';
else
    echo '<a href="/post_reader.php?pid='.$pid.'&page='.$page_num.'" class="page_btn">最后一页</a>';
?>
</div>

</body>
</html>