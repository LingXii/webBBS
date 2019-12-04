<?php
function dealjpg($target_img, $w, $h,$x, $y, $original_width, $original_height, $target_width, $target_height,$filename)
{
    // 剪裁
    $source=imagecreatefromjpeg($target_img);  //创建一个新图象
 
    $croped=imagecreatetruecolor($w, $h);      //新建一个真彩色图像
    imagecopy($croped, $source, 0, 0, $x, $y, $original_width, $original_height); //拷贝图像的一部分
 
    // 缩放
    $scale = $target_width / $w;
    $target = imagecreatetruecolor($target_width, $target_height);   //新建一个真彩色图像
    $final_w = intval($w * $scale);
    $final_h = intval($h * $scale);
    imagecopyresampled($target, $croped, 0, 0, 0, 0, $final_w,$final_h, $w, $h);    //重采样拷贝部分图像并调整大小
 
    // 保存
    imagejpeg($target, 'user_headpic/'.$filename.'.jpg');
    imagedestroy($target);
}

function dealpng($target_img,$w,$h,$x,$y,$original_width,$original_height,$target_width,$target_height,$filename)
{
    // 剪裁
    $source=imagecreatefrompng($target_img);
 
    $croped=imagecreatetruecolor($w, $h);      //新建一个真彩色图像
    imagecopy($croped, $source, 0, 0, $x, $y, $original_width, $original_height); //拷贝图像的一部分
 
    // 缩放
    $scale = $target_width / $w;
    $target = imagecreatetruecolor($target_width, $target_height);   //新建一个真彩色图像
    $final_w = intval($w * $scale);
    $final_h = intval($h * $scale);
    imagecopyresampled($target, $croped, 0, 0, 0, 0, $final_w,$final_h, $w, $h);    //重采样拷贝部分图像并调整大小
 
    // 保存
    imagepng($target, 'user_headpic/'.$filename.'.png');
    imagedestroy($target);
}

function deal($target_width,$target_height,$target_img,$filename)
{
    $img_info=getimagesize($target_img);  // 获取原图尺寸
 
    $original_width=$img_info[0];       //原图片宽度
    $original_height=$img_info[1];       //原图片高度
    $original_mime=$img_info['mime'];
    $type=substr($original_mime,6);       //原本$original_mime值为'image/类型'，通过从第六位字符开始截取得到图片类型
 
 
    $target_scale = $target_height/$target_width; //目标图像长宽比
 
    $original_scale = $original_height/$original_width; // 原图片长宽比
 
    if ($original_scale>=$target_scale){  // 过高
        $w = intval($original_width);
        $h = intval($target_scale*$w);
 
        $x = 0;
        $y = ($original_height - $h)/2;
    } else {                              // 过宽
        $h = intval($original_height);
        $w = intval($h/$target_scale);
 
        $x = ($original_width - $w)/2;
        $y = 0;
    }
 
    switch($type){
        case 'jpg':
        case 'jpeg':
            dealjpg($target_img, $w, $h,$x, $y, $original_width, $original_height, $target_width, $target_height, $filename);  //调用处理jpg函数
            break;
        case 'png':
            dealpng($target_img, $w, $h,$x, $y, $original_width, $original_height, $target_width, $target_height, $filename);  //调用处理png函数
            break;
        default:
            echo "图片类型不正确";
            break;
    }
}

function show_headpic_200($uid)
{
    $file_prefix = 'user_headpic/'.$uid;
    if(file_exists($file_prefix.'_200.jpg')) echo '<img src="'.$file_prefix.'_200.jpg">';
    else if(file_exists($file_prefix.'_200.jpeg')) echo '<img src="'.$file_prefix.'_200.jpeg">';
    else if(file_exists($file_prefix.'_200.png')) echo '<img src="'.$file_prefix.'_200.png">';
    else echo '<img src="user_headpic/0_200.jpg">';
}

function show_headpic_60($uid)
{
    $file_prefix = 'user_headpic/'.$uid;
    if(file_exists($file_prefix.'_60.jpg')) echo '<img src="'.$file_prefix.'_60.jpg">';
    else if(file_exists($file_prefix.'_60.jpeg')) echo '<img src="'.$file_prefix.'_60.jpeg">';
    else if(file_exists($file_prefix.'_60.png')) echo '<img src="'.$file_prefix.'_60.png">';
    else echo '<img src="user_headpic/0_60.jpg">';
}

?>