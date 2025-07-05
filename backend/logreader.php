<?php
// 这个模块当时主要是用来查看 workerman.log 的，其实也没什么用了，Docker 镜像中不会有这个
date_default_timezone_set("Asia/Shanghai");
session_start();
$c = md5($_POST['pass']);
$d = $_GET['destroy'];
$_SESSION['logreaderpassed'] = $_SESSION['logreaderpassed'] || 0;
if ($d == 'true') session_destroy();
if ($c == '7fd5d6a2968384a78a338dfd3d5b095f' || !empty($_SESSION['logreaderpassed'])) {
	$_SESSION['logreaderpassed'] = time();
	$ct = file_get_contents('./workerman.log');
	$ct = str_ireplace(PHP_EOL, '<br>', $ct);
	echo $ct;
	$_SESSION['logreaderpassed'] = time();
	exit();
}
session_write_close();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>logreader</title>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
<form action='#' method='post'>
	<p><input type='password' placeholder='???' name='pass' /></p>
	<p><input type='submit' /></p>
</form>