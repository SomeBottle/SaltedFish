<script>
    setaudio(soundResourceBase + 'about.mp3');
</script>
<div class='container'>
    <h2>咸鱼(Final)1.6.1&nbsp;About&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-By SomeBottle</h2>
    <hr>
    <p><img class='aboutimg' src='./aboutimgs/pingzhi.jpg'></img></p>
    <p>&nbsp;&nbsp;游戏灵感来源于我2016.7.6刚开坑就弃坑的一个1v1对战Flash游戏，当时想的便是回合制，</p>
    <p>但是鉴于Flash联机的大限制(其实是不会搞QAQ)，只能实现两人同台电脑...</p>
    <p>于是两年后的暑假，我利用我这菜鸟仅有的网页通信知识，用最蠢的方法写了这个实现联机1v1的游戏（真的感动啊..）</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;此时正值表情包大火之时，我便把咸鱼表情包中的元素进行了加大..</p>
    <ul>
        <li>游戏内所有表情包均来自网络分享</li>
    </ul>
    <p>&nbsp;</p>
    <h3>游戏技术（其实没啥技术）</h3>
    <ul>
        <li>无刷新ajax动态切换</li>
        <li>ajax+PHP框架，<s>comet模式通信（超浪费资源，可惜不会socket）</s></li>
        <li>Websocket通信（workerman现学现用）</li>
        <li>廉价美国服务器+某免费安全cdn加速(没钱#>#)</li>
    </ul>
    <p>&nbsp;</p>
    <h3>游戏模式</h3>
    <p>切换方式：在输入名字时下方有一个小按钮：</p>
    <p><img class='aboutimg' src='./aboutimgs/rengongzhizhang.jpg'></img></p>
    <ul>
        <li>匹配玩家模式：在在线玩家中随机抽取一位与其对战♂</li>
        <li>人工智障模式：与SomeBottle用脚写出来的破AI进行对决！</li>
    </ul>
    <p>&nbsp;</p>
    <h3>游戏机制</h3>
    <ul>
        <li>回合制双人（或人机）对战.</li>
        <li>在不知道法力的情况下在<s>顶上</s>对决.</li>
        <li>随机角色，不同角色暴击率、闪避率都不同.</li>
        <p><img class='aboutimg' src='./aboutimgs/luxun.jpg'></img></p>
        <li><s>看脸</s>运气，鲁迅说过：“咸鱼里，成功=50%的汗水+50%的运气。”(bushi)</li>
        <li>会被秒杀？！不怕，当伤害太高，你会有保本的25HP.</li>
    </ul>
    <p>&nbsp;</p>
    <h3>角色介绍</h3>
    <p><img class='aboutimg' src='./img/normalfish.png'></img></p>
    <ul><strong>庶咸鱼</strong></ul>
    <ul>
        <li>真·咸鱼——咸鱼中的咸鱼</li>
        <li>苟延残喘地活着...经常从水边捡到一些技能</li>
        <li>十分信仰自由♂，相信着自由能带来防住一切的力量..</li>
    </ul>
    <p><img class='aboutimg' src='./img/holofish.gif'></img></p>
    <ul><strong>全息咸鱼</strong></ul>
    <ul>
        <li>通过时间通道穿梭于未来与当前间的全息AI鱼.</li>
        <li>身份是个谜，曾经有控制时间的功能，但是目前只能皱褶破坏时间.</li>
        <li>有些看上去很高大上却很弱鸡的技能，这是因为它总是没有充够电..</li>
    </ul>
    <p><img class='aboutimg' src='./img/wushifish.png'></img></p>
    <ul><strong>武士咸鱼</strong></ul>
    <ul>
        <li>虽曰武士，但自称是从少林寺出来的</li>
        <li>表面就是恨偷懒的样，修炼也不认真，到底来就突刺学得最认真..</li>
        <li>听说他还有点斜视─━ _ ─━✧..</li>
    </ul>
    <p><img class='aboutimg' src='./img/fashifish.png'></img></p>
    <ul><strong>法师咸鱼</strong></ul>
    <ul>
        <li>不是魔法士的那个玩意啦！算是蛮认真的一个家伙</li>
        <li>掌握了救命技能和必备攻击技能..</li>
        <li>但是他蛮不喜欢他的服饰的，因为其从来没有罩住过他的尾巴..</li>
    </ul>
    <p><img class='aboutimg' src='./img/greenfish.png'></img></p>
    <ul><strong>原谅咸鱼</strong></ul>
    <ul>
        <li>被20条鱼甩了的咸鱼，境遇很惨淡，但是心很开阔，学会了原谅。</li>
        <li>“没有什么是原谅解决不了的”这是他说的..</li>
        <li>别看这家伙浑身绿，其实特别耐打！遇到他你可要小心了..</li>
    </ul>
    <p><img class='aboutimg' src='./img/starfish.png'></img></p>
    <ul><strong>明星咸鱼</strong></ul>
    <ul>
        <li>(其实是自称明星)很自大的一个家伙，从蓝翔毕业，开挖掘机拍广告当上了明星</li>
        <li>他十分喜欢黑帮造型，但是扭曲的脸又不怎么配得上.</li>
        <li>另外他是星战迷...裤子里常装着一把激光剑...</li>
    </ul>
    <p>&nbsp;</p>
    <h3>FAQ</h3>
    <p>Q：哈啊♂我怎么不知道技能所需法力啊？！</p>
    <ul>
        <p>A：这个是Cheese起司提的意见，消耗法力全靠自己计算</p>
    </ul>
    <p>Q：这音乐都是从哪来的？</p>
    <ul>
        <p>A：出自KevinMacleod大神之手，来自无版权音乐网站https://incompetech.com</p>
    </ul>
    <p>Q：出现了error#1弹窗？</p>
    <ul>
        <p>A：这个时候多半是与服务器连接出了问题...重新刷新或者等待试试</p>
    </ul>
    <p>&nbsp;</p>
    <h3>小愿望</h3>
    <ul>
        <li>要是有人能提供一个io域名就好了嘿嘿嘿.....</li>
    </ul>
    <p>&nbsp;</p>
    <h3>Donate</h3>
    <p>那个..那个...<span style="text-decoration:line-through;">欧尼酱</span>...能给我点赞助嘛~</p>
    <ul>
        <li>讨饭页面：<a href='https://imbottle.com/#!aboutme/donate' target='_blank' style='color:#AAA;text-decoration:none;'>https://imbottle.com/#!aboutme/donate</a></li>
    </ul>
    <p>&nbsp;</p>
    <h3>Contact</h3>
    <p>有建议和BUG请<span style="text-decoration:line-through;">激情</span>倾情咨询（病句？！）</p>
    <ul>
        <li>邮箱：somebottle[at]outlook.com</li>
    </ul>
    </ul>
    <h4>Code with ❤️.</h4>
</div>
<a class='about' href='javascript:void(0);' onclick='goback()'><strong>返回</strong></a>