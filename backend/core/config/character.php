<?php
/*暴击,miss率计算方法:4/100*/
$fkind = array('normal', 'wushi', 'fashi', 'starfish', 'holofish', 'grfish');
/*庶咸鱼*/
$normal['name'] = '庶咸鱼';
$normal['skill'] = '1,5,8,9';
$normal['pic'] = 'normalfish.png';
$normal['baoji'] = 10;
$normal['miss'] = 20;
$normal['mpback'] = 15;/*回蓝range*/
/*武士咸鱼*/
$wushi['name'] = '武士咸鱼';
$wushi['skill'] = '1,2,7,10';
$wushi['pic'] = 'wushifish.png';
$wushi['baoji'] = 30;
$wushi['miss'] = 35;
$wushi['mpback'] = 27;
/*法师咸鱼*/
$fashi['name'] = '法师咸鱼';
$fashi['skill'] = '1,3,2,6';
$fashi['pic'] = 'fashifish.png';
$fashi['baoji'] = 10;
$fashi['miss'] = 20;
$fashi['mpback'] = 12;
/*明星咸鱼*/
$starfish['name'] = '明星咸鱼';
$starfish['skill'] = '1,4,5,7';
$starfish['pic'] = 'starfish.png';
$starfish['baoji'] = 12;
$starfish['miss'] = 10;
$starfish['mpback'] = 14;
/*全息咸鱼*/
$holofish['name'] = '全息咸鱼';
$holofish['skill'] = '1,11,12,7';
$holofish['pic'] = 'holofish.gif';
$holofish['baoji'] = 27;
$holofish['miss'] = 15;
$holofish['mpback'] = 30;
/*原谅咸鱼*/
$grfish['name'] = '原谅咸鱼';
$grfish['skill'] = '13,14,15,16';
$grfish['pic'] = 'greenfish.png';
$grfish['baoji'] = 12;
$grfish['miss'] = 28;
$grfish['mpback'] = 13;
