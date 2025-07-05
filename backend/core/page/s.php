<body>
	<div class='lefttitle' id='t1'>
		<h2 id='n1'>MyName</h2>
		<h3 id='hp1' class='littlet'>HP:</h3>
		<h3 id='cr1'>CR:托尔</h3>
		<h3 id='pdd1' class='littlet'>新鲜度:</h3>
	</div>
	<div class='righttitle w' id='t2'>
		<h2 id='n2'>EnemyName</h2>
		<h3 id='hp2' class='littlet'>HP:</h3>
		<h3 id='cr2'>CR:坂本</h3>
		<h3 id='pdd2' class='littlet'>新鲜度:</h3>
	</div>
	<div id='nts' class='nts'>
		<h2 class='notice' id='notice'>先出招</h2>
	</div>
	<div class='mask' id='mask'></div>
	<div class='picdiv' id='pd'></div>
	<img src='' class='screenpic' id='spd'></img>
	<div class='leftfish w' id='leftfish'>
		<img src='./img/normalfish.png' id='fish1'></img>
	</div>
	<div class='rightfish w' id='rightfish'>
		<img src='./img/normalfish.png' id='fish2'></img>
	</div>
	<div class='countdown' id='ct'>
		<h3 class='ctnum' id='ctnum'>00</h3>
	</div>
	<div class='skillboard w' id='skboard'>
		<hr>
		<a class='skillbtn' href='javascript:void(0);'>
			<p class='sn'>正在加载技能...</p>
			<p class='introd'>Blablablablabla.....</p>
		</a>
	</div>
</body>
<script src='./jcs/b.js?2333'></script>
<script>
	document.onkeydown = function(event) {
		e = event ? event : (window.event ? window.event : null);
		var currKey = 0;
		currKey = e.keyCode || e.which || e.charCode;
		if (currKey == 13) {
			if (event.keyCode == 13) {
				var a = document.getElementById('msgtxt').value;
				if (a == null || String(a) == 'undefined' || a.match(/^\s*$/)) {
					alert('请输入聊天内容');
				} else {
					if (a.length <= 50) {
						sendmsg(a);
						msgleft(a);
						document.getElementById('msgtxt').value = '';
					} else {
						alert('聊天内容不要多于50个字符！');
					}
				}
			}
		}
	};
	if (ai) {
		setTimeout(function() {
			document.getElementById('msgtxt').style.display = 'none';
		}, 3000);
	}
</script>