<?php
date_default_timezone_set('Asia/Shanghai');
$conn = connect_db('localhost', 'web_user', '');

function connect_db($host,$user_name,$password)
{
    $conn = mysqli_connect($host, $user_name, $password);
    if(!$conn) die("数据库服务器发生错误：".mysqli_connect_error());
    mysqli_set_charset($conn,'utf8');
    return $conn;
}

function create_db($conn, $name)
{
    $sql = "CREATE DATABASE ".$name;
    if(! mysqli_query($conn,$sql))
        die("创建数据库失败：".mysqli_error($conn));
    echo "数据库".$name."创建成功";
}

function delete_db($conn, $name)
{
    $forbid = array("mysql","sys","information_schema","performance_schema"); // 禁止删除的数据库
    foreach($forbid as $f) if(strcasecmp($name,$f)==0) die("禁止删除此数据库！");
    $sql = "DROP DATABASE ".$name;
    if(! mysqli_query($conn,$sql))
        die("删除数据库失败：".mysqli_error($conn));
    echo "数据库".$name."已删除";
}

function select_db($conn, $name)
{
    if(! mysqli_select_db($conn,$name))
        die("进入数据库失败：".mysqli_error($conn));
    echo "当前数据库：".$name;
    $_SESSION['use_db'] = $name;
}

function echo_table($val)
{
    echo '<br />';  
    $first_in = 1;
    $n = 0;
    while($row = mysqli_fetch_array($val))
    {
        if($first_in)
        {
            $keys = array_keys($row);
            $n = count($keys);      
            if($n == 0) break;
            echo '<table border="1"><tr>';
            for($i=1;$i<$n;$i+=2) echo '<td><b>'.$keys[$i].'</b></td>';
            echo '</tr>';
            $first_in = 0;
        }
        echo '<tr>';
        for($i=1;$i<$n;$i+=2) echo '<td>'.$row[$keys[$i]].'</td>';
        echo '</tr>';      
    }
    if($n == 0) echo "No content.";
    else echo '</table>';
}

function show_dbs($conn)
{
    $sql = "SHOW DATABASES";
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询数据库失败：".mysqli_error($conn));
    echo_table($retval);
}

function show_tbs($conn)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SHOW TABLES";
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询数据表失败：".mysqli_error($conn));
    echo_table($retval);
}

function show_tb($conn,$name)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SELECT * FROM ".$name;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询失败：".mysqli_error($conn));
    echo "→".$name;
    echo_table($retval);
}

function show_tb_attr($conn,$name)
{
    select_db($conn, $_SESSION['use_db']);
    $sql = "SHOW COLUMNS FROM ".$name;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("查询失败：".mysqli_error($conn));
    echo "→".$name;
    echo_table($retval);
}

function execute_sql_debug($conn,$sql)
{
    select_db($conn, $_SESSION['use_db']);
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />语句执行错误：".mysqli_error($conn));  
    if(strcasecmp(substr($sql,0,6),"SELECT")==0)
    {
        $begin = stripos($sql,"FROM ") + 5;
        $end = stripos($sql," ",$begin);
        if(!$end) $end = strlen($sql);
        $name = substr($sql,$begin,$end-$begin);
        echo "→".$name;
        echo_table($retval);
    }
    echo "<br />执行语句成功：".$sql;
}

function query_one($conn,$select,$from,$where_key,$where_value)
{   //调用此函数须确保查询的结果唯一
    $sql = 'select '.$select.' from '.$from.' where '.$where_key.'='.$where_value;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

function find($conn,$select,$from,$where_key,$where_value,$value)
{   //查找是否存在符合条件的值
    $sql = 'select '.$select.' from '.$from.' where '.$where_key.'='.$where_value;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    while($row = mysqli_fetch_array($retval))
    {
        if($value == $row[0]) return True;
    }
    return False;
}

function execute_sql($conn, $sql)
{
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />语句执行错误：".mysqli_error($conn));
    #echo "<br />执行语句成功：".$sql;
    return $retval;
}

function build_web_database($conn)
{
    // TODO: 主码，外码，check，自增等等
    
    // 用户表：uid，账号，密码，邮箱，昵称，头像地址，权限(0游客/封禁，1用户，2管理员，3大老板)
    $table = 'user_info(
            user_id int auto_increment,
            user_name varchar(32) unique,
            user_pwd blob(128),
            user_email varchar(32) unique,
            user_nickname varchar(32),
            user_headpic_url varchar(256),                
            user_permission int,
            primary key (user_id)) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);
    $sql = 'ALTER table sakura.user_info CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.user_info (user_name,user_pwd,user_email,user_nickname,user_permission) 
            values ('boss', PASSWORD('boss'), 'boss@x.com', '博士', 3),
                    ('bokoblin', PASSWORD('bokoblin'), 'bokoblin@zelda.com', '猪', 1),
                    ('moblin', PASSWORD('moblin'), 'moblin@zelda.com', '莫布林', 1),
                    ('momo', PASSWORD('momo'), 'momo@omyoji.com', '桃の花', 1),
                    ('yuki', PASSWORD('yuki'), 'yuki@omyoji.com', '冻住不许走', 1),
                    ('pikachu', PASSWORD('pikachu'), 'pikachu@pokemon.com', '电气老鼠', 1),
                    ('akie', PASSWORD('akie'), 'akie@utami.com', 'Akie秋绘', 1),
                    ('yousa', PASSWORD('yousa'), 'yousa@utami.com', '冷鸟yousa', 1),
                    ('kirlia', PASSWORD('kirlia'), 'kirlia@pokemon.com', 'Lovely', 1);";
    execute_sql($conn, $sql);
    
    // 版面表：bid（1为总版面），名称
    $table = 'board(
            board_id int auto_increment,
            board_name varchar(32) unique,
            primary key (board_id)) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);
    $sql = 'ALTER table sakura.board CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.board (board_name) 
            value ('所有版面'), ('旷野之息'), ('宝可梦'), ('唱见'), ('痒痒鼠'), ('明日方舟');";
    execute_sql($conn, $sql);
    
    // 版面管理表：bid（1为总版面），管理员(uid)
    $table = 'manage(
            bid int,
            uid int,
            foreign key(bid) references sakura.board(board_id) on delete cascade,
            foreign key(uid) references sakura.user_info(user_id) on delete cascade,
            constraint unique_cond UNIQUE (bid,uid)) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);
    $sql = 'ALTER table sakura.board CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.manage (bid,uid) 
            value (1,1), (2,2), (3,6), (4,8), (5,4), (6,7)";
    execute_sql($conn, $sql);   
    
    // 帖子表：pid，标题，所属版面，发帖用户，创建时间，更新时间(最迟回复时间)，
    // 内容(检测越界，内容过多则用文件储存，数据库放文件路径)，
    // 状态(1正常，2违规锁定，3不可回复, 4置顶可回复，5置顶不可回复)
    $table = 'posts(
            post_id int auto_increment,
            post_title varchar(128),
            post_bid int,
            post_uid int,
            post_createtime bigint,
            post_updatetime bigint,
            post_content varchar(16384),
            post_state int,
            primary key (post_id),
            foreign key(post_bid) references sakura.board(board_id) on delete cascade,
            foreign key(post_uid) references sakura.user_info(user_id) on delete cascade) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);
    $sql = 'ALTER table sakura.posts CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.posts (post_title, post_bid, post_uid, post_createtime, post_updatetime, post_content, post_state) 
            values ('净网倡议', 1, 1, 10000000, 10000000, '大家注意下别搞黄色。', 4),
                    ('别再欺负猪猪了！', 2, 2, 10001234, 10001234, '猪猪活得很艰难，猪猪心很累。', 1),
                    ('有能耐把我删了', 3, 6, 10005000, 10005000, '听说剑盾要删一批幸运儿么，不知道有没有我，好紧张啊(=^ ^=)', 1),
                    ('吹爆绘总', 4, 8, 10010010, 10010010, '啊绘总唱歌太好听了，我冷鸟吹爆！', 1),
                    ('莫布林的水帖', 2, 3, 10017000, 10017000, '刚好五个字', 1);";
    execute_sql($conn, $sql);
                
    // 回复表：rid，发回复的用户，所属帖子，回复时间，
    // 内容(检测越界，内容过多则用文件储存，数据库放文件路径)，状态(1正常，2违规)
    $table = 'reply(
            reply_id int auto_increment,
            reply_uid int,
            reply_pid int,
            reply_createtime bigint,
            reply_content varchar(16384), 
            reply_state int,
            primary key (reply_id),
            foreign key(reply_pid) references sakura.posts(post_id) on delete cascade,
            foreign key(reply_uid) references sakura.user_info(user_id) on delete cascade) ENGINE=InnoDB;';
    execute_sql($conn, 'CREATE table if not exists sakura.'.$table);  
    $sql = 'ALTER table sakura.reply CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.reply (reply_uid, reply_pid, reply_createtime, reply_content, reply_state) 
            values (7, 4, 10012000, '啊谢谢冷鸟捧场w', 1),
                    (9, 5, 10018888, '我也水水经验', 1),
                    (2, 5, 10020000, '每日水一水', 1);";
    execute_sql($conn, $sql);

    // 私信
    // state 0=正常 1=sender删除 2=receiver删除 3=双方删除
    $table = 'message(
            msg_id int auto_increment,
            msg_sender int,
            msg_receiver int,
            msg_time bigint,
            msg_content varchar(1024),
            msg_state int,
            primary key (msg_id),
            foreign key (msg_sender) references sakura.user_info(user_id) on delete cascade,
            foreign key (msg_receiver) references sakura.user_info(user_id) on delete cascade)';
    execute_sql($conn, 'CREATE table sakura.'.$table);
    $sql = 'ALTER table sakura.message CONVERT TO CHARACTER SET utf8';
    execute_sql($conn, $sql);
    $sql = "INSERT into sakura.message 
            (msg_sender,msg_receiver,msg_time,msg_content,msg_state) 
            values (1, 2, 100022, 'hello', 0),
            (2, 1, 100031, 'hi. how are you?', 0),
            (1, 2, 100045, 'i\'m fine, and you?', 0),
            (2, 1, 100062, 'i\'m die.', 0),
            (3, 1, 123456, 'awsl', 0),
            (4, 5, 130000, 'yukiちゃん，私信功能上线了诶', 0),
            (5, 4, 130014, '是啊momoちゃん，好像挺好用呢', 0),
            (5, 4, 130028, '我们不如来测试测试bug吧(\#^.^#)', 0),
            (4, 5, 130035, '好啊！', 0),
            (4, 5, 130049, '先测试看看两个人同时发消息会是什么情况吧~', 0),
            (5, 4, 130063, 'ok，那么就从我下一条消息之后数7秒，一起发一条测试吧？', 0),
            (4, 5, 130070, '好', 0),
            (5, 4, 130077, '开始', 0),
            (5, 4, 130084, 'test', 0),
            (4, 5, 130084, 'TEST', 0),
            (4, 5, 130091, '然后换我，从我下一条开始数7秒一起发', 0),
            (5, 4, 130098, '行', 0),
            (4, 5, 130105, '开始', 0),
            (4, 5, 130112, 'TEST', 0),
            (5, 4, 130112, 'test', 0),
            (5, 4, 130119, '看起来比较随机...', 0),
            (4, 5, 130133, '嗯。。好我们再测试看看如果是很长的文本的话，对话框会不会爆炸吧==', 0),
            (5, 4, 130147, '嗯试试看。\n这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本这是很长的文本\n这是好长好长的文本啊！！', 0),
            (4, 5, 130154, '=2-lpgl323=-fpl=l=-dkv=-o,\'23n4ri0ij0\'\\2340m0vm\\\)qwoimrifv=qq\\/s,ospmk20o34-it,-gi-3i4-f-03,-i2-3f-\-0v-033423r23r090<>?\"][[2p=p230f129r123i4\`~2\|3-fmvng0vj=3r0-i1-fk-ovmomefim20icma//./,./,{}::[]]fl,-vr-cmnv=1=0jf0soc,;03cr0nvminb-1n-ifnnvefinnsnniomoomosom\n按法律卡死了可麻烦了开幕式代理费目前我离开你过来口令卡萌沙拉咔玫琳凯马萨莱卡棉\nおさかなのりアップデエルアーダ伝えてアシスタン葵葵補遺おさ￥おいいジャパンネット出掛ける飲まない了解しました', 0),
            (4, 5, 130161, '好像完全没有问题！', 0),
            (5, 4, 130168, '是啊，甚至连对齐和换行都没有出问题呢！', 0),
            (4, 5, 130175, '这样优秀的作业，我想，给个满分都不过分吧？', 0),
            (5, 4, 130182, '同意！满分满分~', 0),
            (4, 5, 130196, '现在才1970年，离作业ddl还有50年呢^_^我先去睡一会啦', 0),
            (5, 4, 130210, '嗯嗯我也先睡了，おやすみなさい～', 0),
            (4, 5, 130217, 'おやすみ～', 0);";
    execute_sql($conn, $sql);
}

function build_database_user($conn)
{
    $sql = "CREATE USER 'web_user'@'localhost' ";
    execute_sql($conn, $sql);
    $sql = "GRANT select,insert,delete,update ON  sakura.* TO 'web_user'@'localhost'";
    execute_sql($conn, $sql);
}

function check_usrpsw($conn,$name,$psw)
{   //检查用户账号密码是否正确，返回uid
    $sql = 'SELECT user_id FROM sakura.user_info WHERE user_name="'.$name.'"'
            . 'AND user_pwd=PASSWORD("'.$psw.'")';
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />发生错误：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

function init($conn)
{
    execute_sql($conn, 'DROP database if exists sakura');
    execute_sql($conn, 'CREATE database sakura');
    build_web_database($conn);
    $_SESSION['uid'] = 0;
}

function check_board_manager($conn,$bid)
{
    if(!find($conn,'uid','sakura.manage','bid','1',$_SESSION['uid']) && 
        !find($conn,'uid','sakura.manage','bid',$bid,$_SESSION['uid']))
        return FALSE;
    else return TRUE;
}

function query_num($conn,$from,$where)
{   //调用此函数须确保查询的结果唯一
    $sql = 'select count(*) from '.$from.' where '.$where;
    $retval = mysqli_query($conn,$sql);
    if(! $retval)
        die("<br />查询失败：".mysqli_error($conn));
    $row = mysqli_fetch_array($retval);
    if(!$row) return NULL;
    else return $row[0];
}

function echo_msg_item($row)
{
    # msg_id, msg_sender, msg_receiver, msg_time, msg_content #
    echo "<div style='min-height: 48px; padding: 0 16px 16px;'>";
    if ($row[1] == $_SESSION['uid'])
    {
        $float = 'right';
        $color = 'green';
    }
    else
    {
        $float = 'left';
        $color = 'black';
    }
    $msg = "<p style='text-align: $float; margin: 0px; color: $color;'>". date('Y-m-d H:i:s', $row[3]) . '</p>' . 
        "<p style='float: $float; margin: 0px;'>". $row[4] . "</p>";
    echo "<div style='float: $float; max-width: 80%;'>".
        $msg. "</div>";
    echo "<div style='clear: both; height: 0;'></div>";
    echo "</div>";
}

?>