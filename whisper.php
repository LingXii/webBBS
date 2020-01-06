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
            array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
            header('Location: whisper.php?uid='.$_GET['uid']);
        }
    }
?>

<?php
    if (isset($_POST['to']))
    {
        #echo $_POST['to'];
        $_SESSION['to'] = $_POST['to'];
    }
    #echo '<br>';
    if (isset($_POST['entrance_uid']))
    {
        $_SESSION['entrance_uid'] = $_POST['entrance_uid'];
        $sql = "INSERT into sakura.message 
                (msg_sender,msg_receiver,msg_time,msg_content,msg_state) 
                values (".$_SESSION['uid'].", ".$_SESSION['entrance_uid'].", ".time().", '', -1);";
        $retval = execute_sql($conn, $sql);
        array_splice($_POST, 0, count($_POST)); // 清空表单并刷新页面，避免再次刷新时重复提交表单
        header('Location: whisper.php?uid='.$_GET['uid']);
    }
    include 'header.php';
    include_once 'style.php';
    $sql = 'SELECT msg_sender + msg_receiver - '.$_SESSION['uid'].' as history from sakura.message
            Where msg_sender = '.$_SESSION['uid'].
                ' or (msg_receiver = '.$_SESSION['uid'].' and msg_state <> -1) 
            Group by msg_sender + msg_receiver
            Order by max(msg_time) desc';   
    
    $retval = execute_sql($conn, $sql);
    #echo_table($retval);
    $whisper_list = array();
    $flag = TRUE;
    $retval = execute_sql($conn, $sql);
    while ($row = mysqli_fetch_array($retval))
    {
        array_push($whisper_list, $row[0]);
        #if ($row[0] == $_SESSION['entrance_uid']) $flag = FALSE;
    }
    #if ($flag) array_unshift($whisper_list, $_SESSION['entrance_uid']);
    #print_r($whisper_list);
?>
<?php
    $iruyo = false;
    echo '<div class="msg-box">';
    echo '<div class="vertical-menu">';
    foreach ($whisper_list as $whisper_uid)
    {
        $iruyo = true;
        $sql = "SELECT user_nickname from sakura.user_info
                where user_id = '$whisper_uid'";
        $retval = execute_sql($conn, $sql);
        $nickname = mysqli_fetch_array($retval)[0];
        if ($whisper_uid == $_SESSION['to'])
            echo '<form method="post" action="">
                <input type="submit" class="active" value = '.$nickname.' />
                <input type="hidden" name="to" value='.$whisper_uid.' />
                </form>';
        else
            echo '<form method="post" action="">
                <input type="submit" value = '.$nickname.' />
                <input type="hidden" name="to" value='.$whisper_uid.' />
                </form>';
    }
    echo '</div>';
    echo '<div class="msg-box-content" id="114514">';
    if (isset($_SESSION['to']))
    {
        $sql = 'SELECT * from sakura.message
            where (msg_sender = '.$_SESSION['uid'].' or msg_receiver = '.$_SESSION['uid'].')
            and msg_sender + msg_receiver = '.($_SESSION['uid'] + $_SESSION['to']).'
            and msg_state <> -1';
        $retval = execute_sql($conn, $sql);
        #echo_table($retval);
        while($row = mysqli_fetch_array($retval))
        {
            echo_msg_item($row);     
        }
        echo '<script type="text/javascript">
                window.onload=function(){
                    var mai=document.getElementById("114514");
                    mai.scrollTop=mai.scrollHeight;
                }
                </script>';
    }
    echo '</div>';
?>
<div class="msg-box-send">
<form method="post" action="" <?php if (!$iruyo) echo 'hidden'?>>
<!--
    <input type="text" name="content" value="" style="width: 544px; height: 77px;" required oninvalid="setCustomValidity('说些什么吧~')" oninput="setCustomValidity('')">
-->
<textarea name="content" value=""
    style="width: 542px; height: 77px; resize: none;"
    required oninvalid="setCustomValidity('说些什么吧~')"
    oninput="setCustomValidity('')"
    placeholder="说些什么吧~"
    autofocus>
</textarea>
<input type="submit" value="发送" style="height: 77px; position: relative; top: -35px;">
<input type="hidden" name="call" value="send_msg">
</form>
</div>
</div>

</body>
</html>