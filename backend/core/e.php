<?php
/*
EXTRA格式：
额外技能1:使用回合,额外技能2:使用回合,.....
本模块处理attack命令时的敌人额外技能
也就是说在对方攻击你时，对方的额外技能才会生效，才会在这里被处理
这里不要处理你的技能
*/
require dirname(__FILE__) . '/config/extraskill.php';
$mainextra = explode(',', $enemyextra);
/*检测是否空白extra*/
$emptynum = 0;
foreach ($mainextra as $val) {
    if (empty($val)) {
        $emptynum += 1;
    }
}
$processtr = '';
$processtr2 = '';
/*开始处理技能*/
if ($emptynum !== count($mainextra)) {
    /*不是空白extra*/
    $rbaoji = $baoji; /*继承一下属性*/
    foreach ($mainextra as $m) {
        if (!empty($m)) {
            $mc = explode(':', $m);
            $etname = $mc[0];
            $ettime = @intval($mc[1]);
            /*开始处理提示*/
            /*etname在extraskill里面是额外技能名*/
            if (isset(${$etname})) {
                if ($ettime > 0) {
                    if ($msgmode == 'normal') {
                        $result['msg'] = $result['msg'] . ${$etname}['othermsg'] . '||';
                    }
                    if ($costhp > 0 && floatval(${$etname}['myhp']) > 0) { /*判断敌人的技能有没有防御*/
                        $player[$nexturn]['hp'] = intval($player[$nexturn]['hp']) + round($costhp * floatval(${$etname}['myhp']));
                        /*防御的攻击百分比计算*/
                        if ((floatval(${$etname}['myhp']) * 100) == 100) {
                            if ($msgmode == 'normal') {
                                $misst = false;
                                /*取消闪避默认提示*/
                                $result['msg'] = $result['msg'] . 'MISS||';
                            }
                        } else {
                            if ($msgmode == 'normal') {
                                $result['msg'] = $result['msg'] . '防御' . (floatval(${$etname}['myhp']) * 100) . '%||';
                            }
                        }
                    }
                    if (${$etname}['greathit']) { /*是否强行暴击*/
                        if (!$rbaoji) {
                            if ($costhp < 0) { /*补血*/
                                $player[$nexturn]['hp'] = intval($player[$nexturn]['hp']) - $costhp * rand(2, 3);
                            } else {
                                $player[$name]['hp'] = intval($player[$name]['hp']) - $costhp * rand(2, 3);
                                if ($msgmode == 'normal') {
                                    $baoji = false;
                                    /*取消暴击提示*/
                                    $result['msg'] = $result['msg'] . '暴击！||';
                                }
                            }
                        }
                    }
                    if (!empty(${$etname}['otherhp'])) { /*判断敌人技能有没有对我造成伤害*/
                        $player[$name]['hp'] = intval($player[$name]['hp']) - floatval(${$etname}['otherhp']); /*默认减去整数*/
                        if ($msgmode == 'normal') {
                            $result['msg'] = $result['msg'] . '嗜血效果！-' . floatval(${$etname}['otherhp']) . 'HP||';
                        }
                    }
                    if (!empty(${$etname}['myfali'])) { /*判断敌人技能有没有新鲜度修改*/
                        $player[$nexturn]['fali'] = intval($player[$nexturn]['fali']) + floatval(${$etname}['myfali']); /*默认加上整数*/
                        if ($msgmode == 'normal') {
                            $result['msg'] = $result['msg'] . '对方新鲜度升高！||';
                        }
                    }
                    if (!empty(${$etname}['otherfali'])) { /*判断敌人技能有没有对我新鲜度修改*/
                        $player[$name]['fali'] = intval($player[$name]['fali']) - floatval(${$etname}['otherfali']); /*默认减去整数*/
                        if ($msgmode == 'normal') {
                            $result['msg'] = $result['msg'] . '你的新鲜度被夺取！||';
                        }
                    }
                    if (!empty(${$etname}['time'])) { /*判断敌人技能有没有对我时间修改*/
                        $extratime = floatval(${$etname}['time']); /*默认增加时间*/
                        if ($msgmode == 'normal') {
                            $result['msg'] = $result['msg'] . '时间被控制了！！||';
                        }
                    }
                    $ettime -= 1;
                    if ($ettime >= 0) { /*使得emsg.php有处理的余地*/
                        $processtr = $processtr . $etname . ':' . $ettime . ',';
                        $processtr2 = $processtr2 . $etname . ',';
                    }
                }
            }
        }
    }
    $player[$nexturn]['extra'] = $processtr;
    $player[$name]['extra'] = $player[$name]['extra'] . $processtr2;
}
