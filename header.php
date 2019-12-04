<!DOCTYPE html>
<html>
<!-- <style>
h1 {color:plum;}
</style> -->
<body>
<?php 
include_once 'style.php';
include_once 'database_util.php';
?>

<?php   
    echo '<div class="header"><a href="/" class="title">'.$title.'</a>';
    if(isset($_GET['bid']) && $_GET['bid']!=1)
    {
        $bid = $_GET['bid'];
        $boardname = query_one($conn,'board_name','sakura.board','board_id',$bid);
        echo '<a class="title_conn"> → </a>';
        echo '<a href="/index.php?bid='.$bid.'" class="title">'.$boardname.'</a>';
        
        if(isset($_GET['pid']))
        {
            $pid = $_GET['pid'];
            $post_title = query_one($conn,'post_title','sakura.posts','post_id',$_GET['pid']);
            echo '<a class="title_conn"> → </a>';
            echo '<a href="/post_reader.php?pid='.$pid.'" class="title">'.$post_title.'</a>';
        }
    }
    else if(isset($_GET['pid']) && !isset($_GET['bid']))
    {
        $pid = $_GET['pid'];
        $post_title = query_one($conn,'post_title','sakura.posts','post_id',$_GET['pid']);
        $bid = query_one($conn,'post_bid','sakura.posts','post_id',$_GET['pid']);
        $boardname = query_one($conn,'board_name','sakura.board','board_id',$bid);
        echo '<a class="title_conn"> → </a>';
        echo '<a href="/index.php?bid='.$bid.'" class="title">'.$boardname.'</a>';
        echo '<a class="title_conn"> → </a>';
        echo '<a href="/post_reader.php?pid='.$pid.'" class="title">'.$post_title.'</a>';
    }
    else if(isset($_GET['uid']))
    {
        $uid = $_GET['uid'];
        $nickname = query_one($conn,'user_nickname','sakura.user_info','user_id',$uid);
        echo '<a class="title_conn"> → </a>';
        echo '<a href="/user_space.php?uid='.$uid.'" class="title">'.$nickname.'的个人空间</a>';
    }

    if($show_buttons)
    {
        if(!isset($_SESSION['uid']) || !$_SESSION['uid'])
        {
            echo '<a href="/sign_up.php" class="topnav">注册</a>';
            echo ' &emsp; &emsp; ';
            echo '<a href="/sign_in.php" class="topnav">登录</a>';
        }
        else
        {
            $conn = connect_db('localhost', 'web_user', '');
            $nickname = query_one($conn,'user_nickname','sakura.user_info',
                    'user_id',$_SESSION['uid']);
            echo '<a href="/user_space.php?uid='.$_SESSION['uid'].'" class="topnav">'.$nickname.'</a>';
        }
    }
    echo '</div>';
;?>

</body>
</html>