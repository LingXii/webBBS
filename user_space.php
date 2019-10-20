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
    $title="Sakura";
    $show_buttons = TRUE;
?>
<?php 
    include_once 'style.php';
    include 'header.php';
    include_once 'database_util.php';
?>
<?php
    if(!isset($_GET['uid'])) die("拒绝访问！");
    $conn = connect_db('localhost','web_user','');
    $str_uid = strval($_GET['uid']);
    if(query_one($conn,'user_id','sakura.user_info','user_id', $str_uid) == NULL)
        die("查无此人！");
?>

<?php 
    $conn = connect_db('localhost', 'web_user', '');
    $str_uid = strval($_GET['uid']);
    $user_name = query_one($conn,'user_name','sakura.user_info',
            'user_id',$str_uid);
    $nickname = query_one($conn,'user_nickname','sakura.user_info',
            'user_id',$str_uid);
    echo '<p><font color="red">'.$nickname.'的个人主页</font></p>';
    echo '<p>账号：'.$user_name.'</p>';
    echo '<p>uid：'.$str_uid.'</p>';
    
    if($_SESSION['uid'] == $_GET['uid'])
    {
        $email = query_one($conn,'user_email','sakura.user_info',
            'user_id',$str_uid);
        echo '<p>邮箱：'.$email.'</p>';
        
        echo '<p>管理的版面：';
        $sql = "SELECT bid FROM sakura.manage WHERE uid = ".strval($_SESSION['uid']);
        $bid_val = mysqli_query($conn,$sql);
        $first_in = TRUE;
        while($bid_row = mysqli_fetch_array($bid_val))
        {
            if(!$first_in) echo ",";
            $first_in = False;
            $str_bid = strval($bid_row[0]);
            $boardname = query_one($conn,'board_name','sakura.board','board_id',$str_bid);
            echo '<a href="/post_manage.php?bid='.$str_bid.'">'.$boardname.'</a>';
        }
        echo '</p>';
    }
?>
    
<form method="post" action="">
<input type="submit" value="退出登录" />
<input type="hidden" name="call" value="15" />
</form>
    
<?php
if(isset($_POST['call']))
{
    if($_POST['call']=="15")
    {
        $_SESSION['uid'] = 0;
        header('Location: index.php');
    }
}
?>

</body>
</html>