<p class='st'>请输入名字</p>
<h2 class='input'><input type='text' id='na' class='na'></input></h2>
<style>
    #pcheck {
        transition: 0.5s ease;
    }
</style>
<a id='aibtn' class='pcheck' href='javascript:void(0);' onclick='playai()'>我想和人工智障van♂</a>
<div id='nts' class='nts'></div>
<script>
    var allowenter = true;
    document.onkeydown = function(event) {
        e = event ? event : (window.event ? window.event : null);
        var currKey = 0;
        currKey = e.keyCode || e.which || e.charCode;
        if (currKey == 13) {
            if (allowenter) {
                if (event.keyCode == 13) {
                    var a = document.getElementById('na').value;
                    if (a == null || String(a) == 'undefined' || a.match(/^\s*$/)) {
                        alert('请输入合法的昵称');
                    } else {
                        if (a.length <= 12) {
                            username = a;
                            t('w', 't');
                            allowenter = false;
                        } else {
                            alert('昵称不要过长！');
                        }
                    }
                }
            }
        }
    };
    checkai(); /*检查模式*/
</script>