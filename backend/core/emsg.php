<?php
/*
EXTRA消息处理
*/
require dirname(__FILE__) . '/config/extraskill.php';
foreach ($exskills as $val) {
	if (stripos($player[$name]['extra'], $val) !== false) {
		if ($val !== 'baoji') {/*暴击变量名特殊处理*/
			$return['msg'] = $return['msg'] . ${$val}['mymsg'] . '||';
		} else {
			$return['msg'] = $return['msg'] . $baojit['mymsg'] . '||';
		}
	}
}
