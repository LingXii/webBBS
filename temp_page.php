<?php
    session_start();
    if(!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta http-equiv="refresh" charset="utf-8" content="3;url=index.php">
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
    if(isset($_POST['call']))
    {
            
    }
    include_once 'style.php';
    include 'header.php';  
?>

修改成功，3秒后返回主页。

</body>
</html>