<?php
/*
AI人工智障对战
*/

require_once __DIR__ . '/../vendor/autoload.php'; // 2025.7.5 改为 composer
use Workerman\Worker;
use Workerman\Timer;

@require dirname(__FILE__) . '/config/config.php';
if (!empty($token)) {
    @require dirname(__FILE__) . '/room/' . $nroom . '.php';
    if (empty($enemyname) || !isset($enemyname)) {
        foreach ($player as $key => $another) {
            if ($key !== $name) {
                $GLOBALS['enemyname'] = $key;
            }
        }
    }
    $name = $enemyname;
    $ainame = $name;
    /*先找到AI名字再说*/
    //if($nturn==$name){
    $dr = $name;
    /*找到AI的敌人*/
    /*重新初始化*/
    @require dirname(__FILE__) . '/config/skill.php';
    @require dirname(__FILE__) . '/config/character.php';
    $baoji = false;
    $misst = false;
    $msgmode = 'ai';
    /*调整信息模式为ai*/
    /*判断出招内容*/
    $aigskill = '';
    $lefthp = $player[$name]['hp'];
    $leftfali = $player[$name]['fali'];
    $ucr = $player[$name]['cr'];
    $mskill = ${$ucr}['skill'];
    $aiskill = explode(',', $mskill);
    $mbaoji = ${$ucr}['baoji'];
    $mmiss = ${$ucr}['miss'];
    /*打乱技能列表，保证AI选择是随机的20200729*/
    shuffle($aiskill);
    if (intval($lefthp) <= 120) {
        /*开始进入危机状态*/
        if (rand(1, 3) == 2) {
            /*随机找最基础*/
            if (empty($aigskill)) {
                foreach ($aiskill as $val) {
                    if ($skills[$val]['fl'] == 0) {
                        $aigskill = $val;
                        break;
                    }
                }
            }
        }
        /*优先找能防身的*/
        foreach ($aiskill as $val) {
            if ($skills[$val]['hp'] <= 0 && $skills[$val]['fl'] <= $leftfali && !empty($skills[$val]['fl'])) {
                $aigskill = $val;
                break;
            }
        }
        if (rand(1, 5) == 2) {
            /*随机找攻击高的20200729*/
            foreach ($aiskill as $val) {
                if ($skills[$val]['hp'] > 0 && $skills[$val]['fl'] <= $leftfali && !empty($skills[$val]['fl'])) {
                    $aigskill = $val;
                    break;
                }
            }
        }
        /*其次找能冻结时间的，前提是要在非冻结模式下，防止重复发招20200728*/
        if (empty($aigskill) && $connectionfreeze <= 0) {
            foreach ($aiskill as $val) {
                if ($skills[$val]['freezetime'] > 0 && $skills[$val]['fl'] <= $leftfali && !empty($skills[$val]['fl'])) {
                    $aigskill = $val;
                    break;
                }
            }
        }
        /*其次找攻击高的*/
        if (empty($aigskill)) {
            foreach ($aiskill as $val) {
                if ($skills[$val]['hp'] > 0 && $skills[$val]['fl'] <= $leftfali && !empty($skills[$val]['fl'])) {
                    $aigskill = $val;
                    break;
                }
            }
        }
        /*其次找最基础*/
        if (empty($aigskill)) {
            foreach ($aiskill as $val) {
                if ($skills[$val]['fl'] == 0) {
                    $aigskill = $val;
                    break;
                }
            }
        }
    } else {
        if (rand(1, 3) == 2) {
            /*随机找最基础*/
            if (empty($aigskill)) {
                foreach ($aiskill as $val) {
                    if ($skills[$val]['fl'] == 0) {
                        $aigskill = $val;
                        break;
                    }
                }
            }
        }
        /*优先找能冻结时间的，前提是要在非冻结模式下，防止重复发招20200728*/
        if ($connectionfreeze <= 0) {
            foreach ($aiskill as $val) {
                if ($skills[$val]['freezetime'] > 0 && $skills[$val]['fl'] <= $leftfali && !empty($skills[$val]['fl'])) {
                    $aigskill = $val;
                    break;
                }
            }
        }
        /*一定几率省法力玩法20200729*/
        if (rand(1, 3) == 2) {
            /*随机找最省法力的20200729*/
            foreach ($aiskill as $val) {
                if ($skills[$val]['fl'] <= 25 && $skills[$val]['fl'] <= $leftfali) {
                    $aigskill = $val;
                    break;
                }
            }
        }
        /*其次找攻击高的*/
        if (empty($aigskill)) {
            foreach ($aiskill as $val) {
                if ($skills[$val]['hp'] > 0 && $skills[$val]['fl'] <= $leftfali && !empty($skills[$val]['fl'])) {
                    $aigskill = $val;
                    break;
                }
            }
        }
        /*其次找能防身的*/
        if (empty($aigskill)) {
            foreach ($aiskill as $val) {
                if ($skills[$val]['hp'] <= 0 && $skills[$val]['fl'] <= $leftfali && !empty($skills[$val]['fl'])) {
                    $aigskill = $val;
                    break;
                }
            }
        }
        /*其次找最基础*/
        if (empty($aigskill)) {
            foreach ($aiskill as $val) {
                if ($skills[$val]['fl'] == 0) {
                    $aigskill = $val;
                    break;
                }
            }
        }
    }
    /*判断完毕*/
    /*以下内容与判断有共同之处*/
    $extratime = 0;
    /*额外时间*/
    $extras = $skills[$aigskill]['extra'];
    $freezetime = $skills[$aigskill]['freezetime']; /*是否冻结时间20200728*/
    /*额外功效录入*/
    $chp = $skills[$aigskill]['hp'];
    $costfali = $skills[$aigskill]['fl'];
    $costhp = rand(intval($chp) / 2, $chp);
    if (randbool($mbaoji)) {
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
            $peoplename = $key;
            /*找到人类玩家的名字再说*/
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
                /*补血*/
                $player[$name]['hp'] = intval($player[$name]['hp']) - $costhp;
                /*costhp<0*/
            } else {
                $player[$key]['hp'] = intval($player[$key]['hp']) - $costhp;
                if (floatval($player[$key]['hp']) <= 0 && $costhp >= 75) {
                    $player[$key]['hp'] = 25; /*伤害太大，保本25HP*/
                }
            }
            if ($freezetime > 0) { /*有冻结时间20200728*/
                $ws->rooms[$nroom]['counterfreeze'] = $freezetime;
                $ws->rooms[$nroom]['counterfreezer'] = $name; /*定义自己为冻结人*/
            }
            if ($baoji && $costhp >= 0) {
                /*暴击处理*/
                $player[$key]['extra'] = $player[$key]['extra'] . 'baoji,';
            }
            if ($misst && $costhp >= 0) {
                /*MISS处理*/
                $player[$key]['extra'] = $player[$key]['extra'] . 'miss,';
            }
            $player[$name]['chuzhao'] = $aigskill;
            /*出招传输*/
            if (floatval($player[$key]['hp']) <= 0) {
                /*判断是否赢了*/
                $player[$key]['hp'] = 0;
                $winner = $name;
                $return['win'] = $name; /*注意是return不是result*/
            }
        } else {
            $player[$key]['chuzhao'] = $aigskill;
            /*出招传输*/
        }
    }
    if (!empty($extras) && stripos($player[$name]['extra'], $extras) == false) {
        /*不能重复使用同样的额外技能*/
        $player[$name]['extra'] = $player[$name]['extra'] . $extras . ',';
        /*增加储存自己招数的额外功效*/
    }
    /*给人类玩家回蓝*/
    if (intval($player[$peoplename]['fali']) <= $configs['maxfali']) {
        $tcr = $player[$peoplename]["cr"];
        $frand = ${$tcr}['mpback'];
        $player[$peoplename]['fali'] = intval($player[$peoplename]['fali']) + rand(1, spcount(300, $frand, intval($player[$peoplename]['hp'])));
    } else {
        $player[$peoplename]['fali'] = $configs['maxfali'];
    }
    $player[$name]['fali'] = intval($player[$name]['fali']) - $costfali;
    /*AI不需要看到消息*/
    require dirname(__FILE__) . '/e.php';
    $connectionfreeze = intval($ws->rooms[$nroom]['counterfreeze']); /*获得当前链接的冻结状态20200728*/
    if ($connectionfreeze > 0) { /*时间冻结中20200728*/
        $nexturn = $name; /*下一回合还是这个人*/
        $ws->rooms[$nroom]['counterfreeze'] = $connectionfreeze - 1; /*冻结回合-1*/
    } else {
        $ws->rooms[$nroom]['counterfreezer'] = ''; /*删除时间冻结人，以便判断*/
    }
    $fstr = '<?php $ai="' . $ai . '";$firstturn="' . $firstturn . '";$nturn="' . $nexturn . '";$player=' . var_export($player, true);
    $fstr = $fstr . ';$winner="' . $winner . '";$count="' . $count . '";$cd="' . $cd . '";?>';
    /*--------------------这里有个延时计时器，模拟人思考的时间段！20200728---------------------------*/
    $delay = Timer::add(rand(2, 5), function () use (&$delay, &$connection, &$return, &$ws, &$nroom, &$fstr, &$name, &$dname, &$player, &$winner, &$ainame, &$peoplename, &$skills, &$ai, &$firstturn, &$nturn, &$count, &$extratime, &$connectionfreeze) {
        file_put_contents(dirname(__FILE__) . '/room/' . $nroom . '.php', $fstr);
        //}
        /*--------------------------------AI广播给敌方-----------------------------------------*/
        $return['msg'] = 'nomsg';
        $return['win'] = 'no';
        $return['ok'] = 'notok';
        if (file_exists(dirname(__FILE__) . '/room/' . $nroom . '.php')) {
            require dirname(__FILE__) . '/room/' . $nroom . '.php';
            /*REQUIRE AGAIN*/
            if (!empty($winner)) {
                $return['win'] = $winner;
            }
            $dname = $ainame;
            /*备份自己的名字*/
            $name = $peoplename;
            /*重定义名字为敌方*/
            $return['msg'] = str_ireplace('nomsg', '', $return['msg']);
            /*替换掉默认文本*/
            require dirname(__FILE__) . '/emsg.php';
            /*处理额外技能消息*/
            require dirname(__FILE__) . '/config/skill.php';
            $return['pic'] = $skills[intval($player[$dname]['chuzhao'])]['pic'];
            $return['sd'] = $skills[intval($player[$dname]['chuzhao'])]['sound'];
            $return['result'] = 'ok';
            if (!empty($return['sd'])) {
                $player[$dname]['chuzhao'] = '';
            }
            $fstr = '<?php $ai="' . $ai . '";$firstturn="' . $firstturn . '";$nturn="' . $nturn . '";$player=' . var_export($player, true) . ';$winner="' . $winner . '";$count="' . $count . '";$cd="' . $cd . '";?>';
            file_put_contents(dirname(__FILE__) . '/room/' . $nroom . '.php', $fstr);
            /*取出敌人的连接*/
            @require dirname(__FILE__) . '/room/' . $nroom . '.php';
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
            /*延迟发送战斗反馈*/
            $ws->rooms[$nroom]['countdown'] = 10;
            /*让ai也有修改time的特殊技能-20200728修改*/
            $ws->rooms[$nroom]['countdown'] = intval($ws->rooms[$nroom]['countdown']) - $extratime;
            /*进入下一回合，重置countdown*/
            $connection->send(json_encode($return, true));
            Timer::del($delay);
            /*类似于setTimeout*/
            /*AI时间冻结再次攻击处理模块*/
            if ($connectionfreeze > 0) {
                $ws->rooms[$nroom]['aiattack'] = true; /*示意在倒计时中途再次攻击*/
            }
        }
    });
}
