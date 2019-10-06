<!DOCTYPE html>
<html>
<!-- <style>
h1 {color:plum;}
</style> -->
<body>
    <?php 
    include_once 'database_util.php';
    ?>
    
    <h1>
        <font color='plum'>
            <?php echo $title;?>
        </font>
    </h1>
    <?php
        if($show_buttons)
        {
            if(!isset($_SESSION['uid']) || !$_SESSION['uid'])
            {
                echo '<a href="/sign_up.php">注册</a>';
                echo ' &emsp; &emsp; ';
                echo '<a href="/sign_in.php">登录</a>';
            }
            else
            {
                $conn = connect_db('localhost', 'root', '');
                $str_uid = strval($_SESSION['uid']);
                $nickname = qurey_one($conn,'user_nickname','sakura.user_info',
                        'user_id',$str_uid);
                echo '<a href="/user_space.php?uid='.$str_uid.'">'.$nickname.'</a>';
            }
        }
    ;?>

</body>
</html>