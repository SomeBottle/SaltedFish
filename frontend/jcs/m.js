var step = 0;
var username = '';
var enemyname = '';
var ai = false;
var room = '';
var timer;
var turn = 0;
/*上一轮是谁*/
var winflag = false;
/*checkfbBUG修复*/
var prepage = new Array();
/*预加载的页面*/
/*两个线程*/
var retry = 0;
var server = '';
var counttime = 0;
/*初始化Server选择*/
if (localStorage.fishserver == undefined || localStorage.fishserver == null || localStorage.fishserver == '') {
    localStorage.fishserver = defaultserver;
}
server = servers[localStorage.fishserver].core;
wslink = servers[localStorage.fishserver].ws;
gso(server, '');
function switchserver(n) {
    localStorage.fishserver = n;
    alert('服务器已经切换为： ' + servers[n].name);
    location.reload();
}
/*打开websocket连接*/
var ws, wst, heartb;
ws = new WebSocket(wslink);
ws.onclose = function () {
    console.log('[Connection]Connection Failed..');
    clearInterval(heartb);
    wst = setTimeout(function () {
        if (ws.readyState == 3) {
            clearInterval(wst);
            alert('与游戏服务器失去连接...');
            t('nows', 't');
            document.getElementById('b').style.display = 'none';
        }
    },
        1000);
}
ws.onopen = function () {
    retry = 0;
    console.log('[Connection]Successfully made....');
    heartb = setInterval(heartbeat, 5000);
    /*发送心跳*/
    startmain();
    /*开始进入主页面*/
}
ws.onmessage = function (e) {
    var datat = eval("(" + e.data + ")");
    if (datat.type == 'chatmsg') {
        /*接受消息模块*/
        msgright(datat.chatmsg);
    } else if (datat.type == 'msg') {
        /*弹送消息模块*/
        if (datat.msg == '[leavetheroom]') {
            alert(enemyname + '离开了房间...');
            logout();
            /*重新创造连接，注意！不能用ws.close!*/
            clearInterval(timer);
            t('m', 't');
        } else {
            analymsg(datat.msg);
        }
    } else if (datat.type == 'getfirst') {
        /*返回属性模块*/
        document.getElementById('hp1').innerHTML = 'HP:' + datat[username].hp;
        document.getElementById('hp2').innerHTML = 'HP:' + datat[enemyname].hp;
        turnred('hp1');
        turnred('hp2');
        document.getElementById('pdd1').innerHTML = '新鲜度:' + datat[username].fl;
        document.getElementById('pdd2').innerHTML = '新鲜度:' + datat[enemyname].fl;
        turnblue('pdd1');
        turnblue('pdd2');
        document.getElementById('cr1').innerHTML = 'CR:' + datat[username].cr;
        document.getElementById('cr2').innerHTML = 'CR:' + datat[enemyname].cr;
        document.getElementById('fish1').src = './img/' + datat[username].cpic;
        document.getElementById('fish2').src = './img/' + datat[enemyname].cpic;
        setTimeout(function () {
            if (datat.firstone == username) {
                turn = 1;
                setTimeout(function () {
                    analymsg('该' + username + '出招了:#009900');
                },
                    1000);
            } else if (datat.firstone == enemyname) {
                turn = 2;
                setTimeout(function () {
                    analymsg('该' + enemyname + '出招了:#009900');
                },
                    1000);
            }
            requestcount();
            /*准备开始倒计时*/
        },
            2000);
    } else if (datat.type == 'countdown') {
        /*接收广播的倒计时*/
        var counttime = datat.ctnum;
        if (counttime < 5) {
            setuaudio(soundResourceBase + 'ftick.mp3');
            document.getElementById('ctnum').style.color = 'red';
        } else {
            setuaudio(soundResourceBase + 'tick.mp3');
            document.getElementById('ctnum').style.color = '#666666';
        }
        if (counttime <= 0) {
            document.getElementById('ctnum').innerHTML = 'NEXT';
        } else {
            document.getElementById('ctnum').innerHTML = counttime;
        }
    } else if (datat.type == 'changeturn') {
        /*通知更换回合*/
        if (!winflag) {
            analymsg('该' + datat.nowturn + '了:#6666FF');
            if (datat.nowturn == username) {
                turn = 1;
            } else if (datat.nowturn == enemyname) {
                turn = 2;
            }
        }
    } else if (datat.type == 'attackreturn') {
        if (datat.ok == 'ok') {
            /*确定正确执行*/
            var userhp = datat[username].hp;
            var userfl = datat[username].fali;
            var enemyhp = datat[enemyname].hp;
            var enemyfl = datat[enemyname].fali;
            var frozen = datat.frozen;/*是否被冻结20200728*/
            var freezer = datat.freezer;/*冻结者是谁20200729，只有attackreturn有！*/
            /*防止出现负血*/
            if (Number(userhp) <= 0) {
                userhp = 0;
            }
            if (Number(enemyhp) <= 0) {
                enemyhp = 0;
            }
            if (frozen == 'yes' && freezer == username) {/*是否被冻结20200728*/
                analymsg('时间冻结，还是你出招:red');
            }
            /*属性增减动画*/
            if (datat.win == 'no') {
                if (userhp !== getnhp('hp1')) {
                    numanimate('HP:', getnhp('hp1'), userhp, 'hp1');
                    turnred('hp1');
                }
                if (enemyhp !== getnhp('hp2')) {
                    numanimate('HP:', getnhp('hp2'), enemyhp, 'hp2');
                    turnred('hp2');
                }
                if (userfl !== getnfl('pdd1')) {
                    numanimate('新鲜度:', getnfl('pdd1'), userfl, 'pdd1');
                    turnblue('pdd1');
                }
                if (enemyfl !== getnfl('pdd2')) {
                    numanimate('新鲜度:', getnfl('pdd2'), enemyfl, 'pdd2');
                    turnblue('pdd2');
                }
            }
            if (datat.win !== 'no') {
                if (!winflag) {
                    /*如果有人赢了*/
                    document.getElementById('hp1').innerHTML = '';
                    document.getElementById('hp2').innerHTML = '';
                    document.getElementById('pdd1').innerHTML = '';
                    document.getElementById('pdd2').innerHTML = '';
                    winflag = true;
                    analymsg(datat.win + '咸鱼翻身了OAO！:#CC3300');
                    if (datat.win == username) {
                        setaudio(soundResourceBase + 'win.mp3');
                        showpic('./img/win.jpg');
                        analymsg('恭喜你成功翻身！:#FE2E64');
                        document.getElementById('fish2').src = './img/failfish.jpg';
                    } else {
                        setaudio(soundResourceBase + 'erquanyingyue.mp3');
                        showpic('./img/dead.jpg');
                        showpic('./img/fail.jpg');
                        analymsg('Oh..你再起不能...:#FE2E64');
                        document.getElementById('fish1').src = './img/failfish.jpg';
                    }
                    setTimeout(function () {
                        t('m', 't');
                    },
                        5000);
                    logout();
                }
            }
            turn = frozen == 'yes' ? 1 : 2;/*如果被冻结了下一轮还是这个人20200728*/
            setuaudio(soundResourceBase + datat.sd);
            showpic('./img/' + datat.pic);
            if (datat.msg !== 'nomsg' && datat.msg !== '') {
                if (!winflag) {
                    analymsg(datat.msg);
                }
            }
            l2r();
        } else {
            if (datat.msg !== 'nomsg' && datat.msg !== '') {
                analymsg(datat.msg);
            }
        }
    } else if (datat.type == 'beattackreturn') {
        /*敌方接受战斗反馈*/
        if (datat.ok == 'ok') {
            /*确定正确执行*/
            var userhp = datat[username].hp;
            var userfl = datat[username].fali;
            var enemyhp = datat[enemyname].hp;
            var enemyfl = datat[enemyname].fali;
            var frozen = datat.frozen;/*是否被冻结20200728*/
            /*防止出现负血*/
            if (Number(userhp) <= 0) {
                userhp = 0;
            }
            if (Number(enemyhp) <= 0) {
                enemyhp = 0;
            }
            /*属性增减动画*/
            if (datat.win == 'no') {
                if (userhp !== getnhp('hp1')) {
                    numanimate('HP:', getnhp('hp1'), userhp, 'hp1');
                    turnred('hp1');
                }
                if (enemyhp !== getnhp('hp2')) {
                    numanimate('HP:', getnhp('hp2'), enemyhp, 'hp2');
                    turnred('hp2');
                }
                if (userfl !== getnfl('pdd1')) {
                    numanimate('新鲜度:', getnfl('pdd1'), userfl, 'pdd1');
                    turnblue('pdd1');
                }
                if (enemyfl !== getnfl('pdd2')) {
                    numanimate('新鲜度:', getnfl('pdd2'), enemyfl, 'pdd2');
                    turnblue('pdd2');
                }
            }
            if (datat.win !== 'no') {
                if (!winflag) {
                    /*有人赢了*/
                    document.getElementById('hp1').innerHTML = '';
                    document.getElementById('hp2').innerHTML = '';
                    document.getElementById('pdd1').innerHTML = '';
                    document.getElementById('pdd2').innerHTML = '';
                    analymsg(datat.win + '咸鱼翻身了OAO！:#CC3300');
                    if (datat.win == username) {
                        setaudio(soundResourceBase + 'win.mp3');
                        showpic('./img/win.jpg');
                        analymsg('恭喜你成功翻身！:#FE2E64');
                        document.getElementById('fish2').src = './img/failfish.jpg';
                    } else {
                        setaudio(soundResourceBase + 'erquanyingyue.mp3');
                        showpic('./img/dead.jpg');
                        showpic('./img/fail.jpg');
                        analymsg('Oh..你再起不能...:#FE2E64');
                        document.getElementById('fish1').src = './img/failfish.jpg';
                    }
                    winflag = true;
                    turn = 2;
                    logout();
                    setTimeout(function () {
                        t('m', 't');
                    },
                        5000);
                }
            } else {
                setTimeout(function () {
                    if (!winflag && frozen !== 'yes') {/*冻结情况另谈20200728*/
                        analymsg('该你出招了！:red');
                    }
                },
                    1400);
            }
            setuaudio(soundResourceBase + datat.sd);
            showpic('./img/' + datat.pic);
            r2l();
            turn = frozen == 'yes' ? 2 : 1;/*如果被冻结了下一轮还是这个人20200728*/
            if (datat.msg !== 'nomsg' && datat.msg !== '') {
                analymsg(datat.msg);
            }
        } else {
            if (datat.msg !== 'nomsg' && datat.msg !== '') {
                analymsg(datat.msg);
            }
        }
    }
}
window.audionum = 0;
/*解析returnmsg的函数*/
function analymsg(m) {
    var p = m.split('||');
    p.forEach(showmsg);
}
function showmsg(item, index) {
    if (item !== '' && item !== null && item !== undefined) {
        notice(item);
    }
}
/*初始化音频*/
var audios = document.createElement('audio');
audios.setAttribute('autoplay', 'autoplay');
audios.setAttribute('loop', 'loop');
/*移动端触发音频*/
function touchaudio() {
    setuaudio(soundResourceBase + 'tick.mp3');
    setaudio(soundResourceBase + 'tick.mp3');
    document.removeEventListener('touchstart', touchaudio);
}
document.addEventListener('touchstart', touchaudio);
/*移动端触发音频结束*/
function setaudio(url) {/*淡出淡入变换*/
    audios.volume = audios.volume.toFixed(2);
    var at = setInterval(function () {
        if (parseFloat(audios.volume) <= 0.01) {
            audios.setAttribute('src', url);
            audios.volume = 0;
            clearInterval(at);
            var at2 = setInterval(function () {
                if (parseFloat(audios.volume) >= 0.5) {
                    audios.volume = 0.5;
                    audios.play();
                    clearInterval(at2);
                } else {
                    audios.volume = parseFloat(audios.volume) + 0.01;
                }
            }, 20);
        } else {
            audios.volume = parseFloat(audios.volume) - 0.01;
        }
    }, 20);
}
function setuaudio(url) {
    // 2025.7.10: 播放完成后自动移除元素，防止资源浪费
    const audio = document.createElement('audio');
    audio.setAttribute('autoplay', 'autoplay');
    audio.setAttribute('src', url);
    audio.play();

    audio.addEventListener('ended', function () {
        audio.remove();
    });
    window.audionum += 1;
}
function log(name, rooms) {
    var data = {
        tp: 'log',
        id: name,
        rm: rooms
    };
    ws.send(JSON.stringify(data));
}
function logout() {
    var data = {
        tp: 'logout'
    };
    ws.send(JSON.stringify(data));
}
function heartbeat() {
    var data = {
        tp: 'heartbeat',
        rm: room
    };
    ws.send(JSON.stringify(data));
}
setInterval(function () {
    if (retry >= 15) {
        alert('出现了很严重的问题\n这可能是我们的土豆服务器造成的\n以至于重试15次失败！\n请联系管理员');
        t('m', 't');
        retry = 0;
    }
    if (document.getElementById('mask') !== null) {
        if (turn == 1) {
            document.getElementById('mask').style.display = 'none';
        } else {
            document.getElementById('mask').style.display = 'block';
        }
    }
},
    1000);
function getroom() {
    $.ajax({
        type: "post",
        url: server + "x.php?type=getroom",
        data: {
            n: username
        },
        dataType: "text",
        //回调函数接收数据的数据格式
        success: function (msg) {
            var datat = '';
            if (msg != '') {
                datat = eval("(" + msg + ")"); //将返回的json数据进行解析，并赋给data
            }
            data = datat;
            if (data.result == 'ok') {
                console.log('[Connection]创建房间：' + data.id);
                room = data.id;
                log(username, room);
                /*在ws服务器上注册名字*/
            } else {
                alert('创建房间失败ww...');
                t('m', 't');
            }
        },
        error: function (msg) {
            console.warn('Error:Connection Lost.');
        }
    });
}
function t(p, e) {
    setuaudio(soundResourceBase + 'qiehuan.mp3');
    if (p == 's') {/*启动连接*/
        console.log('[Connection]准备开启侦听器');
        setTimeout(function () {
            winflag = false;
            setTimeout(function () {
                console.log('[Connection]连接已经启动');
            },
                3000);
        },
            3000);
    }
    $(function () {
        $('#t').addClass('animated fadeOut');
        setTimeout(function () {
            $('#t').removeClass('animated fadeOut');
            document.getElementById(e).style.opacity = 0;
            document.getElementById(e).innerHTML = '';
            if (prepage[p] !== undefined && prepage[p] !== null && prepage[p] !== '') {
                $('#' + e).html(prepage[p]);
                setTimeout(function () {
                    document.getElementById(e).style.opacity = 100;
                    $('#t').addClass('animated fadeIn');
                },
                    50);
            } else {
                $.ajax({
                    type: "post",
                    url: server + "x.php?type=page",
                    /*切换页面不需要服务器切换*/
                    data: {
                        tp: p
                    },
                    dataType: "text",
                    //回调函数接收数据的数据格式
                    success: function (msg) {
                        var datat = '';
                        if (msg != '') {
                            datat = eval("(" + msg + ")"); //将返回的json数据进行解析，并赋给data
                        }
                        data = datat;
                        if (data.result == 'ok') {
                            $('#' + e).html(data.h);
                            setTimeout(function () {
                                document.getElementById(e).style.opacity = 100;
                                $('#t').addClass('animated fadeIn');
                            },
                                50);
                        } else {
                            alert('页面读取错误QAQ');
                        }
                    },
                    error: function (msg) {
                        console.warn('Error:Connection Lost.');
                    }
                });
            }
        },
            1000);
    });
}
function addwait(name, invite) {
    $.ajax({
        type: "post",
        url: server + "x.php?type=addwait",
        data: {
            n: name,
            inv: invite
        },
        dataType: "text",
        //回调函数接收数据的数据格式
        success: function (msg) {
            var datat = '';
            if (msg != '') {
                datat = eval("(" + msg + ")"); //将返回的json数据进行解析，并赋给data
            }
            data = datat;
            if (data.result == 'ok') {
                console.log('[Connection]添加等待队列..');
            } else {
                alert(data.msg);
                t('m', 't');
                clearInterval(timer);
            }
        },
        error: function (msg) {
            console.warn('Error:Connection Lost.');
        }
    });
}
function callai(name) {
    $.ajax({
        type: "post",
        url: server + "x.php?type=callai",
        data: {
            n: name
        },
        dataType: "text",
        success: function (msg) {
            var datat = '';
            if (msg != '') {
                datat = eval("(" + msg + ")");
            }
            data = datat;
            if (data.result == 'ok') {
                t('b', 't');
                enemyname = data.r;
                console.log('[Connection]成功分配bot');
            } else {
                alert(data.msg);
                t('m', 't');
            }
        },
        error: function (msg) {
            console.warn('Error:Connection Lost.');
        }
    });
}
function gso(tservers, e) {
    $.ajax({
        type: "post",
        url: tservers + "x.php?type=onlinenum",
        data: {
            n: name
        },
        dataType: "text",
        //回调函数接收数据的数据格式
        success: function (msg) {
            var datat = '';
            if (msg != '') {
                datat = eval("(" + msg + ")"); //将返回的json数据进行解析，并赋给data
            }
            data = datat;
            if (data.result == 'ok') {
                if (e !== '') {
                    document.getElementById(e).innerHTML = data.r;
                }
            }
            $bueue.next();
        },
        error: function (msg) {
            if (e !== '') {
                document.getElementById(e).innerHTML = 'X服务器离线...';
            }
            if (servers[localStorage.fishserver].core == tservers) {
                alert('检测到当前服务器离线，将为你切换默认服务器');
                localStorage.fishserver = '';
                location.reload();
            }
            $bueue.next();
        }
    });
}
function startwait(name) {
    timer = setInterval(function () {
        console.log('[Connection]正在搜索玩家...');
        $.ajax({
            type: "post",
            url: server + "x.php?type=checkplayer",
            data: {
                n: name
            },
            dataType: "text",
            //回调函数接收数据的数据格式
            success: function (msg) {
                var datat = '';
                if (msg != '') {
                    datat = eval("(" + msg + ")"); //将返回的json数据进行解析，并赋给data
                }
                data = datat;
                if (data.result == 'ok') {
                    if (data.r !== 'noplayer') {
                        clearInterval(timer);
                        console.log('[Connection]配对成功：' + data.r);
                        t('b', 't');
                        enemyname = data.r;
                    }
                } else {
                    alert('插队失败ww...');
                    t('m', 't');
                }
            },
            error: function (msg) {
                console.warn('Error:Connection Lost.');
            }
        });
    },
        2700);
}
var onlineflag = 0;
function p() {
    if (step == 0) {
        t('t', 't');
        document.getElementById('b').style.display = 'none';
    }
    step += 1;
}
function about() {
    t('about', 't');
    document.getElementById('b').style.display = 'none';
}
function gmline() {
    t('servers', 't');
    document.getElementById('b').style.display = 'none';
}
function goback() {
    t('m', 't');
    document.getElementById('b').style.display = 'block';
}
function testscreen() {
    if (parseInt(document.body.clientWidth) <= 320) {
        t('warning', 't');
        document.getElementById('b').style.display = 'none';
    }
}
var retryfirst = 0;
function getfirst() {
    var data = {
        tp: 'getfirst'
    };
    ws.send(JSON.stringify(data));
}
function requestcount() {
    /*请求倒计时*/
    var data = {
        tp: 'requestcountdown'
    };
    ws.send(JSON.stringify(data));
}
function getskillh(iname, e) {
    /*获得技能html*/
    $.ajax({
        type: "post",
        url: server + "r.php?type=getskillh",
        data: {
            n: iname
        },
        dataType: "text",
        //回调函数接收数据的数据格式
        success: function (msg) {
            var datat = '';
            if (msg != '') {
                datat = eval("(" + msg + ")"); //将返回的json数据进行解析，并赋给data
            }
            data = datat;
            if (data.result == 'ok') {
                document.getElementById(e).innerHTML = '<input id=\'msgtxt\' placeholder=\'回车发射聊天OAO\' type=\'text\' class=\'msgc\'></input><hr>' + data.h;
            } else {
                retry += 1;
                console.log('错误数据：' + data);
                if (retry <= 15) return getskillh(iname, e);
            }
        },
        error: function (msg) {
            console.warn('Error:Connection Lost.');

        }
    });
}
function sendmsg(nr) {
    /*发送消息*/
    var data = {
        tp: 'sendmsg',
        id: enemyname,
        content: nr
    };
    ws.send(JSON.stringify(data));
}
function attack(skilln) {
    if (!winflag) {
        /*发送技能*/
        var data = {
            tp: 'attack',
            sk: skilln
        };
        ws.send(JSON.stringify(data));
    }
}
function getnhp(e) {
    return parseInt(document.getElementById(e).innerHTML.replace('HP:', ''));
}
function getnfl(e) {
    return parseInt(document.getElementById(e).innerHTML.replace('新鲜度:', ''));
}
function startmain() {
    console.log('[Connection]Preloading Pages.');
    $.ajax({
        /*预加载页面*/
        type: "post",
        url: server + "x.php?type=preloadpage",
        data: {
            n: 'none'
        },
        dataType: "text",
        success: function (msg) {
            if (msg != '') {
                datat = eval("(" + msg + ")");
            }
            if (datat.result == 'ok') {
                for (var i in datat) {
                    prepage[i] = datat[i];
                }
                setTimeout(function () {
                    document.getElementById('starter').innerHTML = 'By SomeBottle';
                    t('m', 't'); //初始化界面
                    document.getElementById('b').style.display = 'block';
                },
                    1000);
            }
        },
        error: function (msg) {
            console.warn('[Connection]Error:Connection Lost.');

        }
    });
    document.getElementById('sm').innerHTML = '连接成功 WELCOME.';
}
if (!window.WebSocket) {
    /*不支持websocket*/
    setTimeout(function () {
        t('nows', 't');
        document.getElementById('b').style.display = 'none';
    },
        3000);
} else {
    testscreen();
}