<script>
	setaudio(soundResourceBase + 'about.mp3');
</script>
<div class='container'>
	<h2>咸鱼(Final)1.6.1&nbsp;游戏服务器节点</h2>
	<ul id='n'>当前节点：</ul>
	<hr>
	<ul>
		<li>恳求大佬们提供一点稳定的国内节点QAQ...</li>
	</ul>
	<h3>节点列表</h3>
	<ul id='servert'></ul>
	<div class='about' id='sc'><a class='abtxt' href='javascript:void(0);' onclick='goback()'><strong>返回</strong></a></div>
	<script>
		var s = '';
		$('#n').html($('#n').html() + servers[localStorage.fishserver].name);
		for (var cserver in servers) {
			$('#servert').html($('#servert').html() + "<li><a class='abtxt' href='javascript:void(0);' onclick='switchserver(\"" + cserver + "\");'>" + servers[cserver].name + "</a>&nbsp;&nbsp;在线：<span id='" + cserver + "'></span></li>");
			s = s + "<script>$bueue.c(function(){gso(servers['" + cserver + "'].core,'" + cserver + "');});<\/script>";
		}
		$('#sc').html($('#sc').html() + s);
		$bueue.re();
		$bueue.start();
	</script>
</div>