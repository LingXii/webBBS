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
    include_once 'database_util.php';
    include_once 'image_util.php';
?>
<?php
    if (!isset($_GET['uid'])) die("拒绝访问！");
    $str_uid = strval($_GET['uid']);
    if (query_one($conn,'user_id','sakura.user_info','user_id', $str_uid) == NULL)
        die("查无此人！");
?>
    
<?php
    if(isset($_POST['call']))
    {
        if ($_POST['call']=="15")
        {
            $_SESSION['uid'] = 0;
            header('Location: index.php');
        }
        
        if($_POST['call']=="34")
        {
            if ($_FILES["file"]["error"] == 1)
                die('文件大小不可超过2MB');
            if ($_FILES["file"]["error"] > 1)
                die("Error: " . $_FILES["file"]["error"] . "<br />");
            $division = pathinfo($_FILES['file']['name']);
            $extensionName = $division['extension']; 
            if ($extensionName != "jpg" && $extensionName != "jpeg" && $extensionName != "png")
                die('请上传jpg, jpeg, png格式文件');
            deal(200,200,$_FILES['file']['tmp_name'],$_SESSION['uid']."_200");
            deal(60,60,$_FILES['file']['tmp_name'],$_SESSION['uid']."_60");
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            array_splice($_FILES, 0, count($_POST));
            header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );  
            header("Cache-Control: no-cache, must-revalidate" );  // 清除浏览器缓存，否则显示出错
            header('Location: user_space.php?uid='.$_GET['uid']);
        }      
    }
    include_once 'style.php';
    include 'header.php';  
?>

<?php 
    $str_uid = strval($_GET['uid']);
    $user_name = query_one($conn,'user_name','sakura.user_info',
            'user_id',$str_uid);
    $nickname = query_one($conn,'user_nickname','sakura.user_info',
            'user_id',$str_uid);
    
    echo '<div class="userright">';
    echo '<p>账号：'.$user_name.'</p>';
    echo '<p>uid：'.$str_uid.'</p>';
    
    
    if ($_SESSION['uid'] == $_GET['uid'])
    {
        $email = query_one($conn,'user_email','sakura.user_info',
            'user_id',$str_uid);
        echo '<p>邮箱：'.$email.'</p>';
        
        echo '<p>管理的版面：';
        $sql = "SELECT bid FROM sakura.manage WHERE uid = ".strval($_SESSION['uid']);
        $bid_val = mysqli_query($conn,$sql);
        $first_in = TRUE;
        while ($bid_row = mysqli_fetch_array($bid_val))
        {
            if (!$first_in) echo ",";
            $first_in = False;
            $str_bid = strval($bid_row[0]);
            $boardname = query_one($conn,'board_name','sakura.board','board_id',$str_bid);
            echo '<a class="button" href="/post_manage.php?bid='.$str_bid.'">'.$boardname.'</a>';
        }
        echo '</p>';    
    }
    if ($_SESSION['uid'] == $_GET['uid'])
    {
        echo '<form method="post" action="whisper.php?uid='.$_GET['uid'].'">
        <input type="submit" class="button" value="查看消息" />
        </form>';
    }
    if ($_SESSION['uid'] > 0 && $_SESSION['uid'] != $_GET['uid'])
    {
        echo '<form method="post" action="whisper.php?uid='.$_GET['uid'].'">
        <input type="submit" class="button" value="发消息" />
        <input type="hidden" name="to" value='.$_GET['uid'].' />
        <input type="hidden" name="entrance_uid" value='.$_GET['uid'].' />
        </form>';
    }

    if ($_SESSION['uid'] == $_GET['uid'])
    {
        echo '<form method="post" action="">
        <input type="submit" class="button" value="退出登录" />
        <input type="hidden" name="call" value="15" />
        </form>';
    }
    echo '</div>';
    
    echo '<div class="userleft">';
    show_headpic_200($_GET['uid']);  
    if ($_SESSION['uid'] == $_GET['uid'])
    {
        echo '<form action="" method="post" enctype="multipart/form-data">
            <input type="file"name="file"/> 
            <input type="submit" value="更改头像"/>
            <input type="hidden" name="call" value="34"/>
            </form>';
    }
    echo '</div>';
?>

</body>
</html>