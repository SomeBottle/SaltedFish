showpic('./img/dj.jpg');
document.getElementById('n1').innerHTML = username;
document.getElementById('n2').innerHTML = enemyname;
document.getElementById('b').style.display = 'block';
notice('请稍后！正在加载数据');
setTimeout(function () {
    numanimate('HP:', 0, 300, 'hp1');
    numanimate('HP:', 0, 300, 'hp2');
    numanimate('法力值:', 0, 50, 'pdd1');
    numanimate('法力值:', 0, 50, 'pdd2');
}, 300);
setTimeout(function () { getfirst(); console.log('[Connection]Getting Properties.'); }, 2000);
setTimeout(function () { getskillh(username, 'skboard'); document.getElementById('b').style.display = 'none'; console.log('[Connection]Getting Skillboards.'); }, 2700);