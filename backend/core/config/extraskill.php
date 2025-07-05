<?php
$exskills = array('jichudun', 'ziyoudun', 'shuishangpiao', 'baoji', 'miss', 'saibocannon', 'timebrake', 'xuanyun', 'lvxuanfeng', 'lidun');/*数组录入，方便emsg处理*/
/*自己：使用技能方*/
/*基础盾*/
$jichudun['othermsg'] = '对方基础盾！:#CC3300';
$jichudun['mymsg'] = '基础盾！:#CC3300';
$jichudun['greathit'] = false;
$jichudun['myhp'] = +0.3;/* 自己回血/失血量(小数) */
$jichudun['otherhp'] = 0;/* 敌人回血/失血数(整数) */
$jichudun['time'] = 0;/* 时间掌控 */
$jichudun['myfali'] = 0;/* 自己新鲜度掌控 */
$jichudun['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*自由盾*/
$ziyoudun['othermsg'] = '对方自由♂盾！:#0033FF';
$ziyoudun['mymsg'] = '自由♂盾！:#0033FF';
$ziyoudun['greathit'] = false;
$ziyoudun['myhp'] = +0.95;/* 自己回血/失血量(小数) */
$ziyoudun['otherhp'] = 0;/* 敌人回血/失血数(整数) */
$ziyoudun['time'] = 0;/* 时间掌控 */
$ziyoudun['myfali'] = 0;/* 自己新鲜度掌控 */
$ziyoudun['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*波纹疾走*/
$shuishangpiao['othermsg'] = '对方波纹疾走:#0066FF';
$shuishangpiao['mymsg'] = '~波纹疾走~:#0066FF';
$shuishangpiao['greathit'] = false;
$shuishangpiao['myhp'] = +1;/* 自己回血/失血量(小数) */
$shuishangpiao['otherhp'] = 0;/* 敌人回血/失血数(整数) */
$shuishangpiao['time'] = 0;/* 时间掌控 */
$shuishangpiao['myfali'] = 0;/* 自己新鲜度掌控 */
$shuishangpiao['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*赛博加农嗜血*/
$saibocannon['othermsg'] = '对方赛博加农:#0066FF';
$saibocannon['mymsg'] = '~你汲取了对方的HP~:#0066FF';
$saibocannon['greathit'] = false;
$saibocannon['myhp'] = +1.2;/* 自己回血/失血量(小数) */
$saibocannon['otherhp'] = rand(1, 24);/* 敌人回血/失血数(整数) */
$saibocannon['time'] = 0;/* 时间掌控 */
$saibocannon['myfali'] = 0;/* 自己新鲜度掌控 */
$saibocannon['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*时间暂停*/
$timebrake['othermsg'] = '刚才时间暂停了对吧！:#0066FF';
$timebrake['mymsg'] = '时间曾被暂停:#0066FF';
$timebrake['greathit'] = false;
$timebrake['myhp'] = 0;/* 自己回血/失血量(小数) */
$timebrake['otherhp'] = 0;/* 敌人回血/失血数(整数) */
$timebrake['time'] = 0;/* 时间掌控 */
$timebrake['myfali'] = 0;/* 自己新鲜度掌控 */
$timebrake['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*眩晕*/
$xuanyun['othermsg'] = '啊啊啊眩晕了！！:#336600';
$xuanyun['mymsg'] = '对方正在眩晕！:#336600';
$xuanyun['greathit'] = false;
$xuanyun['myhp'] = +1;/* 自己回血/失血量(小数) */
$xuanyun['otherhp'] = 0;/* 敌人回血/失血数(整数) */
$xuanyun['time'] = 0;/* 时间掌控 */
$xuanyun['myfali'] = 0;/* 自己新鲜度掌控 */
$xuanyun['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*绿旋风*/
$lvxuanfeng['othermsg'] = '接受绿色的洗礼吧！:#336600';
$lvxuanfeng['mymsg'] = '~#绿旋风#~:#336600';
$lvxuanfeng['greathit'] = false;
$lvxuanfeng['myhp'] = 0;/* 自己回血/失血量(小数) */
$lvxuanfeng['otherhp'] = rand(0, 16);/* 敌人回血/失血数(整数) */
$lvxuanfeng['time'] = 0;/* 时间掌控 */
$lvxuanfeng['myfali'] = 0;/* 自己新鲜度掌控 */
$lvxuanfeng['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*喝绿茶*/
$lidun['othermsg'] = '对方默默喝了口茶:#336600';
$lidun['mymsg'] = '~好喝！~:#336600';
$lidun['greathit'] = false;
$lidun['myhp'] = 0.6;/* 自己回血/失血量(小数) */
$lidun['otherhp'] = 0;/* 敌人回血/失血数(整数) */
$lidun['time'] = 0;/* 时间掌控 */
$lidun['myfali'] = rand(5, 10);/* 自己新鲜度掌控 */
$lidun['otherfali'] = 0;/* 敌人新鲜度掌控 */
/*暴击*/
$baojit['mymsg'] = '你受到了<暴击！>:#FF3300';
/*MISS*/
$miss['mymsg'] = 'MISS:#FF3300';
