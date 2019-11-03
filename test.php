<!DOCTYPE HTML>
<html>
<body>

<form action="" method="get">
Name: <input type="text" name="usr_name" required="required" />
<input type="submit" value="提交" />
</form>

<form method="post" action="">
    帐号: <input type="text" class="login" name="user_name" required/>
    密码: <input type="password" class="login" name="user_pwd" required oninvalid="setCustomValidity('请填写密码');"/>
    确认密码：<input type="password" class="login" name="user_pwd2" required oninvalid="setCustomValidity('请填写密码');"/>
    邮箱: <input type="text" class="login" name="user_email" required oninvalid="setCustomValidity('请填写邮箱');"/>
    昵称: <input type="text" class="login" name="user_nickname"/>
    <input type="submit" class="login" value="注册"/>
    <input type="hidden" name="call" value="12"/>
</form>

<form action="">
    <label>
        數字: <input type="text" required oninvalid="setCustomValidity('請輸入11位數字')" oninput="setCustomValidity('')">
    </label>
    <input type="submit" value="">
</form>

<?php
    $timestamp = 3611;
    echo date('YmdHis', $timestamp);
    echo 'aaa aaa';
?>

</body>
</html>
