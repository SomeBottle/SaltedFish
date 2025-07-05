/*动画部分233*/
picid = 0;
ntnum = 0;
window.noticen = 0;
/*提示数量*/
function rotate(e, d) {
    $('#' + e).rotate({
        animateTo: d
    });
}
function mtaleft(e) {
    rotate(e, 60);
    $('#' + e).animate({
        left: '50%',
        top: '10%'
    },
        function () {
            rotate(e, 0);
            $('#' + e).animate({
                left: '60%',
                top: '20%'
            },
                function () {
                    $('#' + e).animate({
                        left: '50px',
                        top: '20%'
                    },
                        function () {
                            setTimeout(function () {
                                $('#leftfish').css('transform', 'scaleX(-1)');
                                $('#leftfish').css('-webkit-transform', 'scaleX(-1)');
                                $('#leftfish').css('-moz-transform', 'scaleX(-1)');
                                $('#leftfish').css('-ms-transform', 'scaleX(-1)');/*适配浏览器*/
                            },
                                500);
                            rotate(e, 0);
                        });
                });
        });
}
function checkai() {
    if (!ai) {
        $('#aibtn').html('我想和人工智障van♂');
        notice('匹配玩家模式..:#A9A9F5');
    } else {
        $('#aibtn').html('我想和真鱼互拍♂');
        notice('和bot对战模式..:#A9A9F5');
    }
}
function playai() {
    document.getElementById('aibtn').style.opacity = 0;
    setTimeout(function () {
        if (ai) {
            ai = false;
            $('#aibtn').html('我想和人工智障van♂');
            notice('匹配玩家模式..:#A9A9F5');
        } else {
            ai = true;
            $('#aibtn').html('我想和真鱼互拍♂');
            notice('和bot对战模式..:#A9A9F5');
        }
        setTimeout(function () {
            document.getElementById('aibtn').style.opacity = 1;
        },
            100);
    }, 500);
}
function mtaright(e) {
    rotate(e, 60);
    $('#' + e).animate({
        right: '50%',
        top: '10%'
    },
        function () {
            rotate(e, 0);
            $('#' + e).animate({
                right: '60%',
                top: '20%'
            },
                function () {
                    $('#' + e).animate({
                        right: '50px',
                        top: '20%'
                    });
                });
        });
}
function turnred(e) {
    $('#' + e).css({
        color: 'red'
    });
    setTimeout(function () {
        $('#' + e).css({
            color: '#AAA'
        });
    },
        1000);
}
function turnblue(e) {
    $('#' + e).css({
        color: 'blue'
    });
    setTimeout(function () {
        $('#' + e).css({
            color: '#AAA'
        });
    },
        1000);
}
function grz(e) {
    $('#' + e).animate({
        top: '10%'
    },
        100,
        function () {
            setuaudio(soundResourceBase+'shua.mp3');
            $('#' + e).animate({
                top: '30%'
            },
                100,
                function () {
                    $('#' + e).animate({
                        top: '10%'
                    },
                        100,
                        function () {
                            $('#' + e).animate({
                                top: '30%'
                            },
                                100,
                                function () {
                                    $('#' + e).animate({
                                        top: '20%'
                                    });
                                });
                        });
                });
        });
}
function l2r() {
    mtaleft('leftfish');
    setTimeout(function () {
        grz('rightfish');
    },
        600);
}
function r2l() {
    mtaright('rightfish');
    setTimeout(function () {
        grz('leftfish');
    },
        600);
}
function notice(no) {
    window.noticen = parseInt(window.noticen) + 1;
    var nownt = window.ntnum;
    /*创建元素*/
    var ntdiv = document.getElementById('nts');
    var h2 = document.createElement("h2");
    h2.id = 'notice' + nownt;
    h2.className = 'notice';
    ntdiv.appendChild(h2);
    document.getElementById('notice' + nownt).style.opacity = 0;
    mains = no.split(':');
    if (mains[1] !== null && mains[1] !== undefined && mains[1] !== '') {
        document.getElementById('notice' + nownt).style.color = mains[1];
    }
    document.getElementById('notice' + nownt).innerHTML = mains[0];
    document.getElementById('notice' + nownt).style.display = 'block';
    document.getElementById('notice' + nownt).style.top = 25 + 10 * (parseInt(window.noticen) - 1) + '%';
    $('#notice' + nownt).animate({
        opacity: 1
    },
        500,
        function () {
            setTimeout(function () {
                $('#notice' + nownt).animate({
                    opacity: 0
                },
                    500,
                    function () {
                        document.getElementById('notice' + nownt).style.display = 'none';
                        ntdiv.removeChild(h2);
                        window.noticen = parseInt(window.noticen) - 1;
                    })
            },
                2000);
        });
    window.ntnum += 1;
}
function msgleft(no) {
    var nownt = window.ntnum;
    /*创建元素*/
    var ntdiv = document.getElementById('nts');
    var h2 = document.createElement("h2");
    h2.id = 'notice' + nownt;
    h2.className = 'msgleft';
    ntdiv.appendChild(h2);
    document.getElementById('notice' + nownt).style.opacity = 0;
    mains = no.split(':');
    if (mains[1] !== null && mains[1] !== undefined && mains[1] !== '') {
        document.getElementById('notice' + nownt).style.color = mains[1];
    }
    document.getElementById('notice' + nownt).innerHTML = mains[0];
    document.getElementById('notice' + nownt).style.display = 'block';
    $('#notice' + nownt).animate({
        opacity: 1
    },
        500,
        function () {
            setTimeout(function () {
                $('#notice' + nownt).animate({
                    opacity: 0
                },
                    500,
                    function () {
                        document.getElementById('notice' + nownt).style.display = 'none';
                        ntdiv.removeChild(h2);
                    })
            },
                1000);
        });
    window.ntnum += 1;
}
function msgright(no) {
    var nownt = window.ntnum;
    /*创建元素*/
    var ntdiv = document.getElementById('nts');
    var h2 = document.createElement("h2");
    h2.id = 'notice' + nownt;
    h2.className = 'msgright';
    ntdiv.appendChild(h2);
    document.getElementById('notice' + nownt).style.opacity = 0;
    mains = no.split(':');
    if (mains[1] !== null && mains[1] !== undefined && mains[1] !== '') {
        document.getElementById('notice' + nownt).style.color = mains[1];
    }
    document.getElementById('notice' + nownt).innerHTML = mains[0];
    document.getElementById('notice' + nownt).style.display = 'block';
    $('#notice' + nownt).animate({
        opacity: 1
    },
        500,
        function () {
            setTimeout(function () {
                $('#notice' + nownt).animate({
                    opacity: 0
                },
                    500,
                    function () {
                        document.getElementById('notice' + nownt).style.display = 'none';
                        ntdiv.removeChild(h2);
                    })
            },
                1000);
        });
    window.ntnum += 1;
}
function showpic(u) {
    var arr = u.split(',');
    arr.forEach(function (value, index, array) {
        drawpic(value);
    });
}
function drawpic(u) {
    var s = u.split(':');
    if (s[1] == 'fullshake') {
        document.getElementById('spd').style.display = 'block';
        document.getElementById('spd').src = s[0];
        $('#spd').animate({
            opacity: '0.6'
        },
            500,
            function () {
                $('#spd').addClass('animated wobble duration');
            });
        setTimeout(function () {
            $('#spd').animate({
                opacity: '0'
            },
                500,
                function () {
                    $('#spd').removeClass('animated wobble duration');
                    document.getElementById('spd').src = '';
                    document.getElementById('spd').style.display = 'none';
                });
        },
            1500);
    } else {
        var pnowid = picid;
        $("#pd").append("<img src='" + s[0] + "' class='pic' id='pict" + picid + "' style='height:200%;opacity:0;'></img>");
        $("#pict" + pnowid).animate({
            opacity: '1',
            height: '100%'
        },
            500,
            function () {
                setTimeout(function () {
                    $("#pict" + pnowid).animate({
                        opacity: '0'
                    },
                        500);
                },
                    1200);
            });
        setTimeout(function () {
            $("#pict" + pnowid).remove();
        },
            2200);
        picid += 1;
    }
}
function numanimate(txt, fromn, ton, e) {
    var a = document.getElementById(e);
    var n1 = Number(fromn);
    var n2 = Number(ton);
    var t;
    if (n1 > n2) {
        t = setInterval(function () {
            if (n1 > n2) {
                a.innerHTML = txt + n1;
                n1 = n1 - Math.floor(Math.random() * 10) + 2;
            } else {
                n1 = n2;
                a.innerHTML = txt + n1;
                clearInterval(t);
            }
        },
            10);
    } else if (n1 < n2) {
        t = setInterval(function () {
            if (n1 < n2) {
                a.innerHTML = txt + n1;
                n1 = n1 + Math.floor(Math.random() * 10) + 2;
            } else {
                n1 = n2;
                a.innerHTML = txt + n1;
                clearInterval(t);
            }
        },
            10);
    }
}