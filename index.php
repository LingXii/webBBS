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
    $bid = 1;
    if(isset($_GET['bid'])) $bid = $_GET['bid'];
    $boardname = query_one($conn,'board_name','sakura.board',
            'board_id',$bid);
    if($boardname == NULL) die("版面不存在！"); 
    // $title="Sakura 版面：".$boardname;
    // if($bid == 1) $title="Sakura";
    $title="Sakura";
    $show_buttons = TRUE;
    if(isset($_GET['page'])) $page = $_GET['page'];
    else $page = 1;
    $post_per_page = 30;
?>
<?php 
    include 'header.php';
?>  

<div>
    <?php
//    if(isset($_POST['call']))
//    {
//        if($_POST['call']=="31")
//        {
//            $time = time();
//            $state = '1';
//            if(isset($_POST['replyable'])) $state = '3';
//            $sql = "insert into sakura.posts (post_title,post_bid,post_uid,post_createtime,post_updatetime,"
//                    ."post_content,post_state) value ('".$_POST['title']."',".$bid.",".$_SESSION['uid']
//                    .",".$time.",".$time.",'".$_POST['content']."',".$state.")";
//            execute_sql($conn, $sql);
//        }
//        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
//        header('Location: index.php?bid='.$bid);
//    }
?>
</div>

<br />
<div>
<?php
if($bid > 1)
{
    $post_num = query_one($conn,'count(*)','sakura.posts','post_bid',$bid);
    $page_num = ceil($post_num/$post_per_page);
    if($page_num <= 0) $page_num = 1;
    
    echo '<table border="1" id="posts"><tr>';
    echo '<th><b>帖子主题</b></th>';
    echo '<th><b>发帖用户</b></th>';
    echo '<th><b>发帖时间</b></th>';
    echo '<th><b>最新回复时间</b></th>';
    echo '</tr>';

    $sql = "SELECT * FROM sakura.posts WHERE post_bid = ".$bid." AND post_state >= 4 ORDER BY post_updatetime DESC";
    $post_val = mysqli_query($conn,$sql);
    if(! $post_val) die("查询数据库失败：".mysqli_error($conn));
    $row_cnt = 0;
    while($row = mysqli_fetch_array($post_val))
    {
        $row_cnt += 1;
        if($row_cnt <= ($page-1)*$post_per_page) continue;
        if($row_cnt > $page*$post_per_page) break;
        if($row_cnt%2 == 0) echo '<tr class="posteven">';
        else echo '<tr class="postodd">';
        echo '<td width="66%"><a href="/post_reader.php?pid='.$row[0].'" class="t">'.$row[1].'</a></td>';
        $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[3]);
        echo '<td width="10%"><a href="/user_space.php?uid='.$row[3].'">'.$user_nickname.'</a></td>';
        $createtime = date('Y-n-j H:i:s',$row[4]);
        echo '<td width="12%">'.$createtime.'</td>';
        $updatetime = date('Y-n-j H:i:s',$row[5]);
        echo '<td width="12%">'.$updatetime.'</td>';
        echo '</tr>';              
    }
    $sql = "SELECT * FROM sakura.posts WHERE post_bid = ".$bid." AND post_state <= 3 AND post_state <> 2 ORDER BY post_updatetime DESC";
    $post_val = mysqli_query($conn,$sql);
    if(! $post_val) die("查询数据库失败：".mysqli_error($conn));

    while($row = mysqli_fetch_array($post_val))
    {
        $row_cnt += 1;
        if($row_cnt <= ($page-1)*$post_per_page) continue;
        if($row_cnt > $page*$post_per_page) break;
        if($row_cnt%2 == 0) echo '<tr class="posteven">';
        else echo '<tr class="postodd">';
        echo '<td width="66%"><a href="/post_reader.php?pid='.$row[0].'">'.$row[1].'</a></td>';
        $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[3]);
        echo '<td width="10%"><a href="/user_space.php?uid='.$row[3].'">'.$user_nickname.'</a></td>';
        $createtime = date('Y-n-j H:i:s',$row[4]);
        echo '<td width="12%">'.$createtime.'</td>';
        $updatetime = date('Y-n-j H:i:s',$row[5]);
        echo '<td width="12%">'.$updatetime.'</td>';
        echo '</tr>';       
    }
    echo '</table>';
}
?>
</div> 

<div>
<?php
if($bid == 1)
{
    echo '<table border="1" id="notice"  style="float:right"><tr>';
    echo '<th><b>论坛公告</b></th>';
    echo '<th><b>发布用户</b></th>';
    echo '</tr>';

    $sql = "SELECT * FROM sakura.posts WHERE post_bid = ".$bid." AND post_state >= 4 ORDER BY post_updatetime DESC";
    $post_val = mysqli_query($conn,$sql);
    if(! $post_val) die("查询数据库失败：".mysqli_error($conn));
    $row_cnt = 1;
    while($row = mysqli_fetch_array($post_val))
    {
        if($row_cnt%2 == 0) echo '<tr class="posteven">';
        else echo '<tr class="postodd">';
        echo '<td width="70%"><a href="/post_reader.php?pid='.$row[0].'" class="t">'.$row[1].'</a></td>';
        $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[3]);
        echo '<td width="30%"><a href="/user_space.php?uid='.$row[3].'">'.$user_nickname.'</a></td>';
        echo '</tr>';  
        $row_cnt += 1;    
    }
    $sql = "SELECT * FROM sakura.posts WHERE post_bid = ".$bid." AND post_state <= 3 AND post_state <> 2 ORDER BY post_updatetime DESC";
    $post_val = mysqli_query($conn,$sql);
    if(! $post_val) die("查询数据库失败：".mysqli_error($conn));

    while($row = mysqli_fetch_array($post_val))
    {
        if($row_cnt%2 == 0) echo '<tr class="posteven">';
        else echo '<tr class="postodd">';
        echo '<td width="70%><a href="/post_reader.php?pid='.$row[0].'">'.$row[1].'</a></td>';
        $user_nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$row[3]);
        echo '<td width="30%><a href="/user_space.php?uid='.$row[3].'">'.$user_nickname.'</a></td>';
        echo '</tr>';   
        $row_cnt += 1;
    }
    echo '</table>';
    
    $sql = "SELECT * FROM sakura.board ORDER BY board_id ASC";
    $board_val = mysqli_query($conn,$sql);
    if(! $board_val)
    die("查询数据库失败：".mysqli_error($conn));

    echo '<table border="1" id="notice"><tr>';
    echo '<th><b>版面一览</b></th>';
    echo '<th><b>版面管理员</b></th>';
    echo '</tr>';
    $row_cnt = 1;
    while($row = mysqli_fetch_array($board_val))
    {
        $bid_ = strval($row[0]);
        if($row_cnt%2 == 0) echo '<tr class="posteven">';
        else echo '<tr class="postodd">';
        echo '<td width="70%"><a href="/index.php?bid='.$bid_.'">'.$row[1].'</a></td>';

        $sql = "SELECT uid FROM sakura.manage WHERE bid = ".strval($row[0]);
        $uid_val = mysqli_query($conn,$sql);
        $managers = '';
        $last_uid = '0';
        $first_in = 1;
        while($uid_row = mysqli_fetch_array($uid_val))
        {
            $str_uid = strval($uid_row[0]);
            if($str_uid == $last_uid) break;
            $last_uid = $str_uid;
            $nickname = query_one($conn,'user_nickname','sakura.user_info',
                    'user_id',$str_uid);
            $nametext = '<a href="/user_space.php?uid='.$str_uid.'">'.$nickname.'</a>';
            if($first_in) 
            {
                $managers = $managers.$nametext;
                $first_in = 0;
            }
            else $managers = $managers.', '.$nametext;
        }

        echo '<td width=30%>'.$managers.'</td>';   
        echo '</tr>';     
        $row_cnt += 1;
    }
    echo '</table>';
}

?>
</div>

<br />
<div>
<?php
if($bid > 1)
{
    if($_SESSION['uid'] != 0)
        echo '<a href="/editor.php?bid='.$bid.'" class="edit_btn">发帖</a>';
    
    if($page==1)
        echo '<a href="/index.php?bid='.$bid.'&page=1" class="npage_btn" style="margin-left:15px;">第一页</a>';
    else
        echo '<a href="/index.php?bid='.$bid.'&page=1" class="page_btn" style="margin-left:15px;">第一页</a>';
    
    if($page==1)
    {
        echo '<a href="/index.php?bid='.$bid.'&page=1" class="npage_btn">1</a>';
        if($page_num>1)
            echo '<a href="/index.php?bid='.$bid.'&page=2" class="page_btn">2</a>';
    }
    else if($page==$page_num)
    {
        if($page>1)
            echo '<a href="/index.php?bid='.$bid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
        echo '<a href="/index.php?bid='.$bid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
    }
    else
    {
        echo '<a href="/index.php?bid='.$bid.'&page='.($page-1).'" class="page_btn">'.($page-1).'</a>';
        echo '<a href="/index.php?bid='.$bid.'&page='.($page).'" class="npage_btn">'.($page).'</a>';
        echo '<a href="/index.php?bid='.$bid.'&page='.($page+1).'" class="page_btn">'.($page+1).'</a>';
    }
    
    if($page==$page_num)
        echo '<a href="/index.php?bid='.$bid.'&page='.$page_num.'" class="npage_btn">最后一页</a>';
    else
        echo '<a href="/index.php?bid='.$bid.'&page='.$page_num.'" class="page_btn">最后一页</a>';
}
?>
</div>

</body>
</html>