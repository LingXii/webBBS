<?php
    session_start();
    if (!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title>Sakura - 登录页面</title>
</head>

<body>
<?php 
    $title="Sakura";
    $show_buttons = FALSE;
?>
<?php 
    include_once 'style.php';
    include 'header.php';
    include_once 'database_util.php';
?>

<?php
    if (isset($_POST['call']))
    {
        if ($_POST['call'] == 'send_msg')
        {
            $sql = "INSERT into sakura.message 
                    (msg_sender,msg_receiver,msg_time,msg_content,msg_state) 
                    values (".$_SESSION['uid'].", ".$_SESSION['to'].", ".time().", '".$_POST['content']."', 0);";
            execute_sql($conn, $sql);
        }
    }
?>

<?php
    if (isset($_POST['to']))
    {
        echo $_POST['to'];
        $_SESSION['to'] = $_POST['to'];
    }
    echo '<br>';
    if (isset($_POST['entrance_uid']))
    {
        $_SESSION['entrance_uid'] = $_POST['entrance_uid'];
        $sql = "INSERT into sakura.message 
                (msg_sender,msg_receiver,msg_time,msg_content,msg_state) 
                values (".$_SESSION['uid'].", ".$_SESSION['entrance_uid'].", ".time().", '', -1);";
        $retval = execute_sql($conn, $sql);
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: whisper.php');
    }
    $sql = 'SELECT msg_sender + msg_receiver - '.$_SESSION['uid'].' as history from sakura.message
            Where msg_sender = '.$_SESSION['uid'].
                ' or (msg_receiver = '.$_SESSION['uid'].' and msg_state <> -1) 
            Group by msg_sender + msg_receiver
            Order by max(msg_time) desc';   
    
    $retval = execute_sql($conn, $sql);
    echo_table($retval);
    $whisper_list = array();
    $flag = TRUE;
    $retval = execute_sql($conn, $sql);
    while ($row = mysqli_fetch_array($retval))
    {
        array_push($whisper_list, $row[0]);
        #if ($row[0] == $_SESSION['entrance_uid']) $flag = FALSE;
    }
    #if ($flag) array_unshift($whisper_list, $_SESSION['entrance_uid']);
    print_r($whisper_list);
    foreach ($whisper_list as $whisper_uid)
    {
        echo '<form method="post" action="">
            <input type="submit" value = "向'.$whisper_uid.'发消息" />
            <input type="hidden" name="to" value='.$whisper_uid.' />
            </form>';
    }
    if(isset($_SESSION['to']))
    {
        $sql = 'SELECT * from sakura.message
            where (msg_sender = '.$_SESSION['uid'].' or msg_receiver = '.$_SESSION['uid'].')
            and msg_sender + msg_receiver = '.($_SESSION['uid'] + $_SESSION['to']).'
            and msg_state <> -1';
        $retval = execute_sql($conn, $sql);
        echo_table($retval);
    }
?>

<form method="post" action="">
<input type="text" name="content" value="">
<input type="submit" value="发送">
<input type="hidden" name="call" value="send_msg">
</form>

</body>
</html>