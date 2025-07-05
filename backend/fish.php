<?php

/**
 * 游戏 WebSocket 长连接处理模块
 */

error_reporting(E_ALL & ~E_NOTICE);

require_once __DIR__ . '/vendor/autoload.php'; // 2025.7.5 改为 composer
use Workerman\Worker;
use Workerman\Timer;
/*引入timer*/

require_once __DIR__ . '/core/config/config.php';
date_default_timezone_set("Asia/Shanghai");
define('HEARTBEAT_TIME', $configs['heart']);
/*定义心跳包*/
$return = array();
function onlineadd($n)
{
    if (!empty($n)) {
        $nowtime = date('Y-m-d H:i:s', time());
        if (!is_dir(__DIR__ . '/core/online')) {
            mkdir(__DIR__ . '/core/online');
            $str = '<?php $onlines = ""; ?>';
            file_put_contents(__DIR__ . '/core/online/online.php', $str);
        }
        require __DIR__ . '/core/online/online.php';
        $onlines[$n] = $nowtime;
        $str = '<?php $onlines = ' . var_export($onlines, true) . '; ?>';
        file_put_contents(__DIR__ . '/core/online/online.php', $str);
    }
}
function randbool($percent)
{
    $p = 100 - intval($percent);
    $pr = rand(1, $p);
    $rg2 = $pr + intval($percent);
    $rd = rand(1, 100);
    if ($rd > $pr && $rd <= $rg2) {
        return true;
    } else {
        return false;
    }
}
function grc($length)
{
    /*Random CHARACTERS*/
    $str = null;
    $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz7823647GYGdghevghwevfghfvgrf_KDQWD
   NUIGHXWUXWJBXHWBXJHXHWBXHWJBJBXWJHVB';
    $max = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}
function checkskill($all, $one)
{
    $allp = explode(',', $all);
    $num = 0;
    foreach ($allp as $v) {
        if (intval($v) == intval($one)) {
            $num = $num + 1;
        }
    }
    if (empty($num)) {
        return false;
    } else {
        return true;
    }
}
function tc($s, $e)
{
    $hour = floor((strtotime($e) - strtotime($s)) % 86400 / 3600);
    $minute = floor((strtotime($e) - strtotime($s)) % 86400 / 60);
    $second = floor((strtotime($e) - strtotime($s)) % 86400 % 60);
    return $hour * 3600 + $minute * 60 + $second;
}
function spcount($totalhp, $osp, $nhp)
{ /*回蓝计算(总血量，鱼的新鲜度(法力)range，鱼的(hp)*/
    $lp = intval($totalhp) - intval($nhp);
    if ($lp <= 0) {
        return 0;
    } else {
        $pc = $lp / intval($totalhp);
        return intval(intval($osp) * $pc);
    }
}
function checkonline()
{
    global $configs;
    //检测超时
    $nowtime = date('Y-m-d H:i:s', time());
    if (file_exists(__DIR__ . '/core/online/online.php')) {
        require __DIR__ . '/core/online/online.php';
        foreach ($onlines as $key => $time) {
            if ((tc($time, $nowtime) >= $configs['roomtimeout']) && file_exists(__DIR__ . '/core/room/index.php')) {
                //房间内超时退出检测
                require __DIR__ . '/core/room/index.php';
                if (file_exists(__DIR__ . '/core/room/' . @$rooms[$key] . '.php')) {
                    unlink(__DIR__ . '/core/room/' . $rooms[$key] . '.php');
                }
                unset($rooms[$key]);
                $str = '<?php $rooms = ' . var_export($rooms, true) . '; ?>';
                file_put_contents(__DIR__ . '/core/room/index.php', $str);
                unset($onlines[$key]);
                @unlink(__DIR__ . '/core/waitline/' . $key . '.php');
            }
        }
        $str = '<?php $onlines = ' . var_export(@$onlines, true) . '; ?>';
        file_put_contents(__DIR__ . '/core/online/online.php', $str);
    }
}

// 2025.7.5 包装一下 Worker，定义几个属性

class CustomWorker extends Worker
{
    public $conid = array(); // 用户 ID 到 IP / 连接信息的映射
    public $rooms = array();
    public $contoname = array(); // 这个好像没有用
}

$ws = new CustomWorker("websocket://0.0.0.0:" . $configs['wsport']);
$ws->count = 1;
$ws->onMessage = function ($connection, $data) {
    global $ws;
    global $configs;
    $a = json_decode($data); // a->id 是用户 ID，a->tp 是操作类型
    $ip = $connection->getRemoteIp();
    if ($a->tp == 'log') {
        // 登录游戏
        if (file_exists(__DIR__ . '/core/room/index.php')) {
            require __DIR__ . '/core/room/index.php'; // 导入所有现存房间 $rooms
            if (!isset($ws->conid[$a->id])) {
                if (array_key_exists($a->id, $rooms)) {
                    /*防止无房bug*/
                    // a->rm 是房间号
                    if ($rooms[$a->id] == $a->rm) {
                        /*防止串房bug*/
                        $ws->conid[$a->id]['ip'] = $ip;
                        $ws->conid[$a->id]['con'] = $connection;
                        /*储存连接名*/
                        $connection->uid = $a->id;
                        $connection->roomid = $a->rm;
                        if (!isset($ws->rooms[$a->rm])) {
                            $ws->rooms[$a->rm]['ctnum'] = 0;
                            /*初始化倒计时*/
                            $ws->rooms[$a->rm]['stoptime'] = false;
                            /*初始化时停技能*/
                        }
                        // 将连接加入房间
                        $ws->rooms[$a->rm]['cons'][$a->id] = $connection;
                        $connection->hearttime = time();
                        /*心跳+1*/
                        echo PHP_EOL . $a->id . ' connected';
                    } else {
                        $return['type'] = 'console';
                        $return['msg'] = 'Join Failed. Room cant be matched with the player.';
                        echo PHP_EOL . $a->id . ' want to join other rooms , denied.';
                    }
                } else {
                    $return['type'] = 'console';
                    $return['msg'] = 'Join Failed. Room cant be matched with the player.';
                    echo PHP_EOL . $a->id . ' want to join the non-existent room , denied.';
                }
            } else {
                echo PHP_EOL . 'Already exists.';
            }
        }
    } else {
        if ($a->tp == 'logout') {
            $thisn = $connection->uid;
            $thisr = $connection->roomid;
            if (isset($connection->uid)) {
                unset($connection->uid);
                unset($connection->roomid);
            }
            if (isset($ws->conid[$thisn])) {
                unset($ws->conid[$thisn]);
                if (isset($ws->rooms[$thisr]['counter'])) {
                    Timer::del($ws->rooms[$thisr]['counter']);
                    /*删除倒计时定时器*/
                }
                unset($ws->rooms[$thisr]);
            }
            echo PHP_EOL . 'Destroyed ' . $thisn . '`s connection.';
        } else {
            if ($a->tp == 'heartbeat') {
                $connection->hearttime = time();
                /*心跳+1*/
                if (isset($connection->roomid)) {
                    $roomt = $connection->roomid;
                    if (!file_exists(__DIR__ . '/core/room/' . $roomt . '.php')) {
                        // 房间消失了
                        $return['type'] = 'msg';
                        $return['msg'] = '[leavetheroom]';
                        $connection->send(json_encode($return, true));
                    } else {
                        require __DIR__ . '/core/room/' . $roomt . '.php';
                        if ($ai == 'yes') {
                            $name = $connection->uid;
                            foreach ($player as $key => $another) {
                                if ($key !== $name) {
                                    onlineadd($key);
                                    /*保持AI在线*/
                                }
                            }
                        }
                    }
                }
            } else {
                if ($a->tp == 'sendmsg') {
                    // 聊天信息
                    $towho = $a->id;
                    $room = $connection->roomid;
                    if (file_exists(__DIR__ . '/core/room/' . $room . '.php')) {
                        require __DIR__ . '/core/room/' . $room . '.php';
                        if ($ai !== 'yes') {
                            if (array_key_exists($towho, $player)) {
                                $content = $a->content;
                                $tocon = $ws->conid[$towho]['con'];
                                $return['type'] = 'chatmsg';
                                $return['chatmsg'] = $content;
                                $tocon->send(json_encode($return, true));
                            }
                        }
                    }
                } else {
                    if ($a->tp == 'getfirst') {
                        $room = $connection->roomid;
                        $name = $connection->uid;
                        if (file_exists(__DIR__ . '/core/room/' . $room . '.php')) {
                            require __DIR__ . '/core/room/' . $room . '.php';
                            if (array_key_exists($name, $player)) {
                                echo PHP_EOL . 'Name: ' . $name . ' Room ' . $room . ' is getting properties.';
                                /*判断是否存在于房间*/
                                $return['type'] = 'getfirst';
                                $return['firstone'] = $firstturn;
                                $return[$name]['hp'] = $player[$name]["hp"];
                                /*先获取自己的玩意*/
                                $return[$name]['fl'] = $player[$name]["fali"];
                                $cr = $player[$name]["cr"];
                                require __DIR__ . '/core/config/character.php';
                                $return[$name]['cr'] = ${$cr}['name'];
                                $return[$name]['cpic'] = ${$cr}['pic'];
                                foreach ($player as $k => $v) {
                                    if ($k !== $name) {
                                        $return[$k]['hp'] = $player[$k]["hp"];
                                        /*再获取敌方的玩意*/
                                        $return[$k]['fl'] = $player[$k]["fali"];
                                        $cr = $player[$k]["cr"];
                                        $return[$k]['cr'] = ${$cr}['name'];
                                        $return[$k]['cpic'] = ${$cr}['pic'];
                                    }
                                }
                            }
                        }
                        $connection->send(json_encode($return, true));
                    } else {
                        if ($a->tp == 'requestcountdown') {
                            /*-------------------------------倒计时处理模块*/
                            $nroom = $connection->roomid;
                            echo PHP_EOL . 'Room ' . $nroom . ' requested for count';
                            if (file_exists(__DIR__ . '/core/room/' . $nroom . '.php')) {
                                @(require __DIR__ . '/core/room/' . $nroom . '.php');
                                if (isset($ws->rooms[$nroom])) {
                                    $ws->rooms[$nroom]['ctnum'] = intval($ws->rooms[$nroom]['ctnum']) + 1;
                                    /*计算等待倒计时开始的人数*/
                                    if (intval($ws->rooms[$nroom]['ctnum']) >= 2 && !isset($ws->rooms[$nroom]['countdown']) || $ai == 'yes') {
                                        /*先计算一遍AI*/
                                        /*开始倒计时*/
                                        $ws->rooms[$nroom]['countdown'] = 10;
                                        if (file_exists(__DIR__ . '/core/room/' . $nroom . '.php')) {
                                            require __DIR__ . '/core/room/' . $nroom . '.php';
                                            if ($ai == 'yes') {
                                                $people = $connection->uid;
                                                if ($firstturn !== $people) {
                                                    $fstr = '<?php $ai = "' . $ai . '";$firstturn = "' . $firstturn . '";$nturn = "' . $people . '";$player = ' . var_export($player, true) . ';$winner = "' . $winner . '";$count = "' . $count . '";$cd = "' . $cd . '"; ?>';
                                                    file_put_contents(__DIR__ . '/core/room/' . $nroom . '.php', $fstr);
                                                    /*如果是AI，先设置倒数为0，跳转到判断AI出招的部分*/
                                                    $ws->rooms[$nroom]['countdown'] = 0;
                                                }
                                            }
                                        }
                                        $ws->rooms[$nroom]['counterfreeze'] = 0; /*计时器冻结回合，为技能作准备20200728*/
                                        $ws->rooms[$nroom]['counterfreezer'] = ''; /*计时器冻结人，为技能作准备20200728*/
                                        $ws->rooms[$nroom]['aiattack'] = false; /*AI是否在倒计时中途进行攻击，用于配合时间冻结20200729*/
                                        $ws->rooms[$nroom]['counter'] = Timer::add(1, function () use ($connection, $ws) {
                                            /*创建定时器*/
                                            /*广播倒计时到指定房间*/
                                            $nroom = $connection->roomid;
                                            foreach ($ws->rooms[$nroom]['cons'] as $c) {
                                                $return['type'] = 'countdown';
                                                $return['ctnum'] = $ws->rooms[$nroom]['countdown'];
                                                $c->send(json_encode($return, true));
                                            }
                                            if (intval($ws->rooms[$nroom]['countdown']) > 0) {
                                                if (!$ws->rooms[$nroom]['stoptime']) {
                                                    $ws->rooms[$nroom]['countdown'] = intval($ws->rooms[$nroom]['countdown']) - 1;
                                                }
                                                if ($ws->rooms[$nroom]['aiattack']) { /*AI示意要在倒计时中途进行攻击20200729*/
                                                    $ws->rooms[$nroom]['aiattack'] = false; /*取消示意*/
                                                    if (file_exists(__DIR__ . '/core/room/' . $nroom . '.php')) {
                                                        require __DIR__ . '/core/room/' . $nroom . '.php';
                                                        $nexturn = $nturn;
                                                        $token = md5(grc(32));
                                                        $peoplename = $connection->uid;
                                                        /*人类玩家名字*/
                                                        $fstr = '<?php $ai = "' . $ai . '";$firstturn = "' . $firstturn . '";$nturn = "' . $nexturn . '";$player = ' . var_export($player, true) . ';$winner = "' . $winner . '";$count = "' . $count . '";$cd = "' . $cd . '"; ?>';
                                                        file_put_contents(__DIR__ . '/core/room/' . $nroom . '.php', $fstr);
                                                        $thenowturn = $nexturn;
                                                        /*备份下一轮的人*/
                                                        if ($ai == 'yes' && empty($winner) && $nexturn !== $peoplename) {
                                                            /*开启AI*/
                                                            $name = $peoplename;
                                                            $enemyname = $nexturn;
                                                            /*--------------------------冻结时间预处理20200728------------------*/
                                                            $connectionfreeze = intval($ws->rooms[$nroom]['counterfreeze']); /*获得当前链接的冻结状态*/
                                                            require __DIR__ . '/core/ai.php';
                                                        }
                                                    }
                                                }
                                            } else {
                                                $ws->rooms[$nroom]['countdown'] = 10;
                                                /*一个回合10秒*/
                                                /*更换回合*/
                                                if (file_exists(__DIR__ . '/core/room/' . $nroom . '.php')) {
                                                    require __DIR__ . '/core/room/' . $nroom . '.php';
                                                    foreach ($player as $key => $another) {
                                                        if ($key !== $nturn) {
                                                            $nexturn = $key;
                                                        }
                                                    }
                                                    $token = md5(grc(32));
                                                    $peoplename = $connection->uid;
                                                    /*人类玩家名字*/
                                                    $fstr = '<?php $ai = "' . $ai . '";$firstturn = "' . $firstturn . '";$nturn = "' . $nexturn . '";$player = ' . var_export($player, true) . ';$winner = "' . $winner . '";$count = "' . $count . '";$cd = "' . $cd . '"; ?>';
                                                    file_put_contents(__DIR__ . '/core/room/' . $nroom . '.php', $fstr);
                                                    $thenowturn = $nexturn;
                                                    /*备份下一轮的人*/
                                                    if ($ai == 'yes' && empty($winner) && $nexturn !== $peoplename) {
                                                        /*开启AI*/
                                                        $name = $peoplename;
                                                        $enemyname = $nexturn;
                                                        /*--------------------------冻结时间预处理20200728------------------*/
                                                        $connectionfreeze = intval($ws->rooms[$nroom]['counterfreeze']); /*获得当前链接的冻结状态*/
                                                        require __DIR__ . '/core/ai.php';
                                                    }
                                                    /*广播更换回合*/
                                                    foreach ($ws->rooms[$nroom]['cons'] as $c) {
                                                        $return['type'] = 'changeturn';
                                                        $return['nowturn'] = $thenowturn;
                                                        $c->send(json_encode($return, true));
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        } else {
                            if ($a->tp == 'attack') {
                                /*--------------------------冻结时间预处理20200728------------------*/
                                $nroom = $connection->roomid; /*获得房间号*/
                                $connectionfreeze = intval($ws->rooms[$nroom]['counterfreeze']); /*获得当前链接的冻结状态*/
                                $connectionfreezer = $ws->rooms[$nroom]['counterfreezer']; /*获得当前链接的冻结人*/
                                /*-------------------------------出招处理模块*/
                                $backupplayer = array();
                                /*备份$player，防止被AI扰乱*/
                                $msgmode = 'normal';
                                $baoji = false;
                                $misst = false;
                                /*暴击、闪避探测初始化*/
                                $result['ok'] = 'notok';
                                $result['msg'] = 'nomsg';
                                $result['win'] = 'no';
                                $extratime = 0;
                                /*额外时间*/
                                $nroom = $connection->roomid;
                                $name = $connection->uid;
                                $gskill = $a->sk;
                                $enemyname = '';
                                /*敌人名字-用于AI判断*/
                                $enename = '';
                                /*敌人名字，用于最后的储存*/
                                if (file_exists(__DIR__ . '/core/room/' . $nroom . '.php')) {
                                    require __DIR__ . '/core/room/' . $nroom . '.php';
                                    $cr = $player[$name]["cr"];
                                    require __DIR__ . '/core/config/character.php';
                                    $sks = ${$cr}['skill'];
                                    $bj = ${$cr}['baoji'];
                                    if ($nturn == $name) {
                                        /*判断是否该你出招*/
                                        if (checkskill($sks, $gskill)) {
                                            /*判断技能存在*/
                                            require __DIR__ . '/core/config/skill.php';
                                            $extras = $skills[$gskill]['extra'];
                                            /*额外功效录入*/
                                            $freezetime = $skills[$gskill]['freezetime']; /*是否冻结时间20200728*/
                                            if (!empty($connectionfreezer) && $freezetime > 0) { /*不能在冻结时间的时候再使用冻结技能20200728*/
                                                $result['msg'] = '不能重复使用该技能！';
                                                $result['ok'] = 'notok';
                                            } else {
                                                $chp = $skills[$gskill]['hp'];
                                                $costfali = $skills[$gskill]['fl'];
                                                $costhp = rand(intval($chp) / 2, $chp);
                                                if (randbool($bj)) {
                                                    $baoji = true;
                                                    $costhp = $costhp * rand(2, 3);
                                                }
                                                /*获取之前的数据*/
                                                $nexturn = '';
                                                $playerextra = '';
                                                $enemyextra = '';
                                                $recentplayerhp = $player[$name]['hp'];
                                                $recentenemyhp = '';
                                                $recentplayerfali = $player[$name]['fali'];
                                                $recentenemyfali = '';
                                                foreach ($player as $key => $another) {
                                                    if ($key !== $name) {
                                                        $enemyname = $key;
                                                        $enename = $key;
                                                        $enemycr = $player[$key]['cr'];
                                                        $enemyms = ${$enemycr}['miss'];
                                                        if (randbool($enemyms)) {
                                                            /*MISS*/
                                                            if ($costhp >= 0) {
                                                                $costhp = 0;
                                                                $misst = true;
                                                            }
                                                        }
                                                        $nexturn = $key;
                                                        $playerextra = $player[$name]['extra'];
                                                        $enemyextra = $player[$key]['extra'];
                                                        $recentenemyhp = $player[$key]['hp'];
                                                        $recentenemyfali = $player[$key]['fali'];
                                                        /*之前数据获取结束*/
                                                        if ($costhp < 0) {
                                                            /*补血，补血肯定是补自己了*/
                                                            $player[$name]['hp'] = intval($player[$name]['hp']) - $costhp;
                                                            /*costhp<0*/
                                                        } else {
                                                            /*失血失敌人的*/
                                                            $player[$key]['hp'] = intval($player[$key]['hp']) - $costhp;
                                                            if (floatval($player[$key]['hp']) <= 0 && $costhp >= 75) {
                                                                $player[$key]['hp'] = 25;
                                                                /*伤害太大，保本25HP*/
                                                            }
                                                        }
                                                        if ($freezetime > 0) { /*有冻结时间20200728*/
                                                            $ws->rooms[$nroom]['counterfreeze'] = $freezetime;
                                                            $ws->rooms[$nroom]['counterfreezer'] = $name; /*定义自己为冻结人*/
                                                            $connectionfreezer = $name; /*更新时间冻结人，不然后面递交给前端无法判断*/
                                                        }
                                                        if ($baoji && $costhp >= 0) {
                                                            /*暴击处理*/
                                                            $player[$key]['extra'] = $player[$key]['extra'] . 'baoji,';
                                                        }
                                                        if ($misst && $costhp >= 0) {
                                                            /*MISS处理*/
                                                            $player[$key]['extra'] = $player[$key]['extra'] . 'miss,';
                                                        }
                                                        $player[$name]['chuzhao'] = $gskill;
                                                        if (floatval($player[$key]['hp']) <= 0) {
                                                            /*判断是否赢了*/
                                                            $player[$key]['hp'] = 0;
                                                            $winner = $name;
                                                            $result['win'] = $name;
                                                        }
                                                    } else {
                                                        $player[$name]['chuzhao'] = $gskill;
                                                    }
                                                }
                                                if (!empty($extras) && stripos($player[$name]['extra'], $extras) == false) {
                                                    /*不能重复使用同样的额外技能*/
                                                    $player[$name]['extra'] = $player[$name]['extra'] . $extras . ',';
                                                    /*增加储存自己招数的额外功效*/
                                                }
                                                $player[$name]['fali'] = intval($player[$name]['fali']) - $costfali;
                                                $result['msg'] = str_ireplace('nomsg', '', $result['msg']);
                                                /*替换掉默认文本*/
                                                require __DIR__ . '/core/e.php';
                                                /*负责处理额外技能的模块*/
                                                if ($baoji && $costhp >= 0) {
                                                    /*暴击处理*/
                                                    $result['msg'] = $result['msg'] . '暴击OAO!!:#FF3300||';
                                                }
                                                if ($misst && $costhp >= 0) {
                                                    /*MISS处理*/
                                                    $result['msg'] = $result['msg'] . '对方MISS:#FF3300||';
                                                }
                                                if ($ai == 'yes' && empty($winner)) {
                                                    /*防止AI导致的双重回调*/
                                                    if (intval($player[$enename]['fali']) <= $configs['maxfali']) {
                                                        $aicr = $player[$enename]["cr"];
                                                        $frand = ${$aicr}['mpback'];
                                                        $player[$enename]['fali'] = intval($player[$enename]['fali']) + rand(1, spcount(300, $frand, intval($player[$enename]['hp'])));
                                                    } else {
                                                        $player[$enename]['fali'] = $configs['maxfali'];
                                                    }
                                                    /*AI回蓝*/
                                                }
                                                $connectionfreeze = intval($ws->rooms[$nroom]['counterfreeze']); /*获得当前链接的冻结状态20200728*/
                                                if ($connectionfreeze > 0) { /*时间冻结中20200728*/
                                                    $nexturn = $name; /*下一回合还是这个人*/
                                                    $ws->rooms[$nroom]['counterfreeze'] = $connectionfreeze - 1; /*冻结回合-1*/
                                                } else {
                                                    $ws->rooms[$nroom]['counterfreezer'] = ''; /*删除时间冻结人，以便判断*/
                                                }
                                                $fstr = '<?php $ai = "' . $ai . '";$firstturn = "' . $firstturn . '";$nturn = "' . $nexturn . '";$player = ' . var_export($player, true);
                                                $fstr = $fstr . ';$winner = "' . $winner . '";$count = "' . $count . '";$cd = "' . $cd . '"; ?>';
                                                /*----------------------以下不要使用$nexturn这个变量----------------------*/
                                                if (intval($player[$name]['fali']) < 0) {
                                                    $result['msg'] = '你的新鲜度不够了！';
                                                    $result['ok'] = 'notok';
                                                } else {
                                                    $ws->rooms[$nroom]['countdown'] = 10;
                                                    /*进入下一回合*/
                                                    $ws->rooms[$nroom]['countdown'] = intval($ws->rooms[$nroom]['countdown']) - $extratime;
                                                    /*额外时间改变*/
                                                    file_put_contents(__DIR__ . '/core/room/' . $nroom . '.php', $fstr);
                                                    $token = md5(grc(32));
                                                    $result['result'] = 'ok';
                                                    $result['pic'] = $skills[intval($gskill)]['pic'];
                                                    $result['sd'] = $skills[intval($gskill)]['sound'];
                                                    $result['ok'] = 'ok';
                                                    $backupplayer = $player;
                                                    /*备份$player*/
                                                    /*人工智障启动！*/
                                                    if ($ai == 'yes' && empty($winner) && $connectionfreeze <= 0) {
                                                        /*防止AI导致的双重回调，且没有时间冻结的情况下启用ai20200728*/
                                                        require __DIR__ . '/core/ai.php';
                                                    } else {
                                                        if ($ai !== 'yes') {
                                                            /*-------------------(receive attack)广播给敌方*/
                                                            $return['msg'] = 'nomsg';
                                                            $return['win'] = 'no';
                                                            $return['ok'] = 'notok';
                                                            if (file_exists(__DIR__ . '/core/room/' . $nroom . '.php')) {
                                                                require __DIR__ . '/core/room/' . $nroom . '.php';
                                                                /*REQUIRE AGAIN*/
                                                                if (!empty($winner)) {
                                                                    $return['win'] = $winner;
                                                                }
                                                                $dname = $name;
                                                                /*备份自己的名字*/
                                                                $name = $enemyname;
                                                                /*重定义名字为敌方*/
                                                                if (intval($player[$name]['fali']) <= $configs['maxfali']) {
                                                                    $tcr = $player[$name]["cr"];
                                                                    $frand = ${$tcr}['mpback'];
                                                                    $player[$name]['fali'] = intval($player[$name]['fali']) + rand(1, spcount(300, $frand, intval($player[$name]['hp'])));
                                                                } else { /*防止法力超过阈值20200729*/
                                                                    $player[$name]['fali'] = $configs['maxfali'];
                                                                }
                                                                /*回蓝*/
                                                                $return['msg'] = str_ireplace('nomsg', '', $return['msg']);
                                                                /*替换掉默认文本*/
                                                                require __DIR__ . '/core/emsg.php';
                                                                /*处理额外技能消息*/
                                                                require __DIR__ . '/core/config/skill.php';
                                                                $return['pic'] = $skills[intval($player[$dname]['chuzhao'])]['pic'];
                                                                $return['sd'] = $skills[intval($player[$dname]['chuzhao'])]['sound'];
                                                                $return['result'] = 'ok';
                                                                if (!empty($return['sd'])) {
                                                                    $player[$dname]['chuzhao'] = '';
                                                                }
                                                                $fstr = '<?php $ai = "' . $ai . '";$firstturn = "' . $firstturn . '";$nturn = "' . $nturn . '";$player = ' . var_export($player, true) . ';$winner = "' . $winner . '";$count = "' . $count . '";$cd = "' . $cd . '"; ?>';
                                                                file_put_contents(__DIR__ . '/core/room/' . $nroom . '.php', $fstr);
                                                                $dcon = $ws->conid[$name]['con'];
                                                                /*取出敌人的连接*/
                                                                if (intval($player[$name]['hp']) <= 0) { /*防止扣血到负数20200729*/
                                                                    $player[$name]['hp'] = 0;
                                                                }
                                                                if (intval($player[$dname]['hp']) <= 0) { /*防止扣血到负数20200729*/
                                                                    $player[$dname]['hp'] = 0;
                                                                }
                                                                if ($connectionfreeze > 0) { /*被冻结20200728*/
                                                                    $return['frozen'] = 'yes';
                                                                    $return['msg'] .= '时间冻结，还是对方出招！:#DF0101||';
                                                                } else {
                                                                    $return['frozen'] = 'no';
                                                                }
                                                                $return[$name]['hp'] = $player[$name]['hp'];
                                                                $return[$name]['fali'] = $player[$name]['fali'];
                                                                $return[$dname]['hp'] = $player[$dname]['hp'];
                                                                $return[$dname]['fali'] = $player[$dname]['fali'];
                                                                $return['ok'] = 'ok';
                                                                $return['type'] = 'beattackreturn';
                                                                $dcon->send(json_encode($return, true));
                                                            } else {
                                                                $return['ok'] = 'notok';
                                                                $return['msg'] = '有人离开了...';
                                                            }
                                                        }
                                                    }
                                                    /*AI end*/
                                                    /*在新鲜度足够的情况下处理冻结信息20200729*/
                                                    !empty($result['msg']) ?: $result['msg'] = ''; /*如果消息是空的20200729*/
                                                    if ($connectionfreeze > 0) { /*被冻结20200728*/
                                                        $result['frozen'] = 'yes';
                                                        $result['freezer'] = $connectionfreezer; /*用于前端判断*/
                                                    } else {
                                                        $result['frozen'] = 'no';
                                                        $result['freezer'] = '';
                                                    }
                                                }
                                            }
                                        } else {
                                            $result['msg'] = '你的职业没有这个技能';
                                            $result['ok'] = 'notok';
                                        }
                                    } else {
                                        $result['msg'] = '目前还没到你出招呢！';
                                        $result['ok'] = 'notok';
                                    }
                                    @(require __DIR__ . '/core/room/' . $nroom . '.php');
                                    $name = $connection->uid;
                                    /*将名字定义回来*/
                                    if (intval(@$backupplayer[$name]['hp']) <= 0) {
                                        $backupplayer[$name]['hp'] = 0;
                                    }
                                    if (intval(@$backupplayer[$enename]['hp']) <= 0) {
                                        $backupplayer[$enename]['hp'] = 0;
                                    }
                                    $result[$name]['hp'] = @$backupplayer[$name]['hp'];
                                    $result[$name]['fali'] = @$backupplayer[$name]['fali'];
                                    $result[$enename]['hp'] = @$backupplayer[$enename]['hp'];
                                    $result[$enename]['fali'] = @$backupplayer[$enename]['fali'];
                                    if ($result['ok'] == 'ok' && $connectionfreeze <= 0) { /*考虑被冻结20200728*/
                                        $result['msg'] .= '该' . $enename . '了:#6666FF||';
                                    }
                                    $result['type'] = 'attackreturn';
                                    $connection->send(json_encode($result, true));
                                    /*广播攻击信息给自己*/
                                } else {
                                    $result['ok'] = 'notok';
                                    $result['msg'] = '有人离开了...';
                                }
                            }
                        }
                    }
                }
            }
        }
    }
};
$ws->onClose = function ($connection) {
    global $ws;
    if (isset($connection->uid)) {
        $dname = $connection->uid;
        unset($ws->conid[$dname]);
        unset($connection->uid);
        $delroom = $connection->roomid;
        if (isset($ws->rooms[$delroom])) {
            if (isset($ws->rooms[$delroom]['counter'])) {
                Timer::del($ws->rooms[$delroom]['counter']);
                /*删除倒计时定时器*/
            }
            unset($ws->rooms[$delroom]);
        }
        unset($connection->roomid);
        unset($connection);
        echo PHP_EOL . $dname . ' disconnected';
    }
};
$ws->onWorkerStart = function ($ws) {
    /*定时计算心跳*/
    Timer::add(2, function () {
        global $ws;
        $time_now = time();
        foreach ($ws->connections as $connection) {
            if (empty($connection->hearttime)) {
                $connection->hearttime = $time_now;
                continue;
            }
            if ($time_now - $connection->hearttime > HEARTBEAT_TIME) {
                if (isset($connection->uid)) {
                    echo PHP_EOL . $connection->uid . ' timed out.';
                    /*无响应连接*/
                }
                $connection->close();
            } else {
                if (isset($connection->uid)) {
                    /*持续心跳包*/
                    onlineadd($connection->uid);
                }
            }
        }
        checkonline();
    });
    echo PHP_EOL . 'Salted Fish Server START!!!!';
    echo PHP_EOL . ' SomeBottle Bless you';
    echo PHP_EOL . '--------------------';
    echo PHP_EOL . '---|GOD BLESS ME|---';
    echo PHP_EOL . '--------------------';
    echo PHP_EOL . '--|DO NOT SHOW ME|--';
    echo PHP_EOL . '--|FXXKING   BUGS|--';
    echo PHP_EOL . '--------------------';
};
Worker::runAll();
