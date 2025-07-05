<?php
header('Access-Control-Allow-Origin:*');
ignore_user_abort(true);
ini_set('max_execution_time', '15');
set_time_limit(15);
/*r.php - 游戏初始化中枢. By SomeBottle*/
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
date_default_timezone_set("Asia/Shanghai");
header('Content-type:text/json;charset=utf-8');
$name = @$_POST['n'];
$type = @$_GET['type'];
$ais = @$_POST['aion'];
$roomc = @$_POST['roomid'];
$result['win'] = 'no';
$result['result'] = 'ok';
$gskill = @$_POST['sk'];
$result['m'] = 'nomsg';
$baoji = false;
$misst = false;
$ene = @$_POST['en'];
$msgmode = 'normal';
function tc($s, $e)
{
    $hour = floor((strtotime($e) - strtotime($s)) % 86400 / 3600);
    $minute = floor((strtotime($e) - strtotime($s)) % 86400 / 60);
    $second = floor((strtotime($e) - strtotime($s)) % 86400 % 60);
    return $hour * 3600 + $minute * 60 + $second;
}
if (strlen($name) <= 12) { //防止名字过长
    if ($type == 'getskillh') {
        require './room/index.php';
        if (file_exists('./room/' . $rooms[$name] . '.php')) {
            $roomc = $rooms[$name];
        } else {
            $roomc = '';
        }
        if (file_exists('./room/' . $roomc . '.php')) {
            require './room/' . $roomc . '.php';
            $cr = $player[$name]["cr"];
            require './config/character.php';
            $html = '';
            $sks = ${$cr}['skill'];
            $epcr = explode(',', $sks);
            require './config/skill.php';
            foreach ($epcr as $skname) {
                $skn = $skills[$skname]['name'];
                $ski = $skills[$skname]['intro'];
                $html = $html . '<a class="skillbtn" href="javascript:void(0);" onclick="attack(' . $skname . ')"><p class="sn">' . $skn . '</p><p>' . $ski . '</p></a>';
            }
            $result['h'] = $html;
        } else {
            $result['result'] = 'notok';
        }
    }
}
echo json_encode($result, true);
