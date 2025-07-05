<?php
header('Access-Control-Allow-Origin:*');
/*x.php - 后端请求路由. By SomeBottle*/
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
date_default_timezone_set("Asia/Shanghai");
header('Content-type:text/json;charset=utf-8');
$p = @$_POST['tp'];
$inv = @$_POST['inv'];
$name = $_POST['n'];
$type = $_GET['type'];
$result['result'] = 'ok';
function grc($length)
{
    $str = null;
    $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $max = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

// player 是玩家名称，iv 是对手名称(邀请者)，ifai 是是否是 AI 对手
function createroom($player, $iv, $ifai)
{ //增加游戏房间
    if (!is_dir('./room')) {
        mkdir('./room');
    }
    if (!file_exists('./room/index.php')) {
        file_put_contents('./room/index.php', '<?php $rooms=array();?>');
    }
    require './room/index.php';
    if (!isset($rooms[$player])) {
        $roomid = md5('room' . date('Y-m-d H:i:s', time()) . rand(1, 100));
        if (!isset($rooms[$player])) {
            $getran = rand(1, 2);
            $fplayer = ''; // 2025.7.5 这里是想确定谁为先手
            if ($getran == 1) {
                $fplayer = $player;
            } else {
                $fplayer = $iv;
            }
            require './config/character.php';
            $chara1 = $fkind[array_rand($fkind)];
            $chara2 = $fkind[array_rand($fkind)];
            // 引入 room.php 后，fplayer 和 chara1、chara2 都会被替换成实际的玩家名称和角色
            require './config/room.php';
            $str2 = $roomstr;
            $str2 = str_ireplace('{ifai}', $ifai, $str2);
            file_put_contents('./room/' . $roomid . '.php', $str2);
        }
        $rooms[$player] = $roomid;
        $rooms[$iv] = $roomid;
        $str = '<?php $rooms=' . var_export($rooms, true) . ';?>';
        file_put_contents('./room/index.php', $str);
    }
}
function getroom($player)
{
    if (!is_dir('./room')) {
        mkdir('./room');
    }
    if (!file_exists('./room/index.php')) {
        file_put_contents('./room/index.php', '<?php $rooms=array();?>');
    }
    require './room/index.php';
    if (isset($rooms[$player])) {
        return $rooms[$player];
    } else {
        return 'Failed to get room ID.';
    }
}
function onlineadd($n)
{
    if (!empty($n)) {
        ob_start();
        $nowtime = date('Y-m-d H:i:s', time());
        if (!is_dir('./online')) {
            mkdir('./online');
            $str = '<?php $onlines=array();?>';
            file_put_contents('./online/online.php', $str);
        }
        require './online/online.php';
        $onlines[$n] = $nowtime;
        $str = '<?php $onlines=' . var_export($onlines, true) . '; ?>';
        file_put_contents('./online/online.php', $str);
        ob_end_clean();
    }
}
function checkonline()
{ //检测超时
    ob_start();
    $nowtime = date('Y-m-d H:i:s', time());
    if (file_exists('./online/online.php')) {
        require './online/online.php';
        foreach ($onlines as $key => $time) {
            if (tc($time, $nowtime) >= 12 && file_exists('./room/index.php')) {
                require './room/index.php';
                if (file_exists('./room/' . $rooms[$key] . '.php')) {
                    unlink('./room/' . $rooms[$key] . '.php');
                }
                unset($rooms[$key]);
                $str = '<?php $rooms=' . var_export($rooms, true) . ';?>';
                file_put_contents('./room/index.php', $str);
                unset($onlines[$key]);
                @unlink('./waitline/' . $key . '.php');
            }
        }
    }
    $str = '<?php $onlines=' . var_export($onlines, true) . '; ?>';
    file_put_contents('./online/online.php', $str);
    /*检查是否有幽灵房间*/
    if (is_dir('./room/')) {
        $roomall = scandir('./room');
        foreach ($roomall as $van) {
            if ($van !== '.' && $van !== '..' && $van !== 'index.php') {
                require './room/' . $van;
                foreach ($player as $oc => $onlinecp) {
                    require './online/online.php';
                    if (array_key_exists($oc, $onlines) !== true) {
                        unlink('./room/' . $van);
                    }
                }
            }
        }
    }
    ob_end_clean();
}
function tc($s, $e)
{
    $hour = floor((strtotime($e) - strtotime($s)) % 86400 / 3600);
    $minute = floor((strtotime($e) - strtotime($s)) % 86400 / 60);
    $second = floor((strtotime($e) - strtotime($s)) % 86400 % 60);
    return $hour * 3600 + $minute * 60 + $second;
}
if (strlen($name) <= 12) { //防止名字过长
    function getrandomf($n)
    {
        if (!is_dir('./waitline')) {
            mkdir('./waitline');
        }
        $r = scandir('./waitline');
        $key = array_rand($r);
        if ($r[$key] !== '..' && $r[$key] !== '.' && $r[$key] !== $n . '.php') {
            require './waitline/' . $r[$key];
            if (!empty($invite)) { //已经有人邀请了
                return 'noplayer';
            } else {
                require './waitline/' . $n . '.php';
                $str1 = '<?php $na="' . $n . '";$invite="' . str_replace('.php', '', $r[$key]) . '";?>';
                $str2 = '<?php $na="' . str_replace('.php', '', $r[$key]) . '";$invite="' . $n . '";?>';
                file_put_contents('./waitline/' . $r[$key], $str2);
                file_put_contents('./waitline/' . $n . '.php', $str1);
                return 'success';
            }
        } else {
            if (count($r) <= 3) {
                return 'noplayer';
            } else {
                return getrandomf($n);
            }
        }
    }
    if (empty($type) || strpos($name, '<') !== false || strpos($name, '?') !== false || strpos($name, '!') !== false) {
        $result['result'] = 'notok';
    }
    if ($type == 'page') { //加载页面
        if (file_exists('./page/' . $p . '.php')) {
            $ht = file_get_contents('./page/' . $p . '.php');
            $result['h'] = $ht;
        } else {
            $result['result'] = 'notok';
        }
    } else if ($type == 'preloadpage') { /*预加载页面*/
        $allpg = scandir('./page');
        foreach ($allpg as $v) {
            if ($v !== '.' && $v !== '..') {
                $ht = file_get_contents('./page/' . $v);
                $nm = basename($v, '.php');
                $result[$nm] = $ht;
            }
        }
        $result['result'] = 'ok';
    } else if ($type == 'addwait') { //添加等待队列
        $onum = 0;
        if (file_exists('./online/online.php')) {
            require './online/online.php';
            $onum = count($onlines);
        }
        require './config/config.php';
        if ($onum < $configs['maxonline']) {
            if (!is_dir('./online')) {
                mkdir('./online');
                $str = '<?php $onlines=array();?>';
                file_put_contents('./online/online.php', $str);
            }
            if (!preg_match("/\s/", $name) && !preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $name) && !is_numeric($name) && !empty($name) && strlen($name) <= 12) {
                if (!preg_match('/[\x{4e00}-\x{9fa5}]/u', $name)) {
                    require './online/online.php';
                    if (array_key_exists($name, $onlines)) {
                        $result['result'] = 'notok';
                        $result['msg'] = '昵称被占用，请稍候~（你可以暂时换个昵称）';
                    } else {
                        if (!is_dir('./waitline')) {
                            mkdir('./waitline');
                        }
                        $str = '<?php $na="' . $name . '";$invite="' . $inv . '";?>';
                        file_put_contents('./waitline/' . $name . '.php', $str);
                        $result['result'] = 'ok';
                    }
                } else {
                    $result['result'] = 'notok';
                    $result['msg'] = '昵称不能有中文';
                }
            } else {
                $result['result'] = 'notok';
                $result['msg'] = '昵称不能有特殊符号，不能有纯数字，长度控制在12以下~';
            }
        } else {
            $result['msg'] = '服务器拥堵了，车上♂满员了。稍后再来~';
            $result['result'] = 'notok';
        }
    } else if ($type == 'checkplayer') { //检查是否有配对
        sleep(1);
        @require './waitline/' . $name . '.php';
        if (!empty($invite)) {
            $result['r'] = $invite;
            @unlink('./waitline/' . $name . '.php');
            createroom($name, $invite, 'no');
        } else {
            $result['r'] = 'noplayer';
        }
        $g = getrandomf($name);
        $result['result'] = 'ok';
    } else if ($type == 'getroom') {
        $result['id'] = getroom($name);
    } else if ($type == 'onlinenum') {
        if (file_exists('./online/online.php')) {
            require './online/online.php';
            $result['r'] = count($onlines);
        }
    } else if ($type == 'callai') { /*分配bot*/
        if (!is_dir('./online')) {
            mkdir('./online');
            $str = '<?php $onlines=array();?>';
            file_put_contents('./online/online.php', $str);
        }
        if (!preg_match("/\s/", $name) && !preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $name) && !is_numeric($name) && !empty($name) && strlen($name) <= 12) {
            if (!preg_match('/[\x{4e00}-\x{9fa5}]/u', $name)) {
                require './online/online.php';
                if (array_key_exists($name, $onlines)) {
                    $result['result'] = 'notok';
                    $result['msg'] = '昵称被占用，请稍候~（你可以暂时换个昵称）';
                } else {
                    $botname = grc(6);
                    onlineadd($botname);
                    createroom($name, $botname, 'yes');
                    $result['result'] = 'ok';
                    $result['r'] = $botname;
                }
            } else {
                $result['result'] = 'notok';
                $result['msg'] = '昵称不能有中文';
            }
        } else {
            $result['result'] = 'notok';
            $result['msg'] = '昵称不能有特殊符号，不能有纯数字，长度控制在12以下~';
        }
    } else {
        $result['result'] = 'notok';
    }
    onlineadd($name);
    checkonline();
} else {
    $result['result'] = 'notok';
}
echo json_encode($result, true);
