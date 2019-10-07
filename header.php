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

    
<div class="header">
<a href="/" class="title">
    <?php echo $title;?>
</a>
    
<?php
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
            $str_uid = strval($_SESSION['uid']);
            $nickname = qurey_one($conn,'user_nickname','sakura.user_info',
                    'user_id',$str_uid);
            echo '<a href="/user_space.php?uid='.$str_uid.'" class="topnav">'.$nickname.'</a>';
        }
    }
;?>

</div>
</body>
</html>