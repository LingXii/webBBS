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
    include 'header.php';
    include_once 'database_util.php';
?>
<p>SakuraSakura Ai Tai Yo ~</p>

</body>
</html>