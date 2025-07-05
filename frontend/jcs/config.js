var musiclist = ['https://resources.xbottle.top/Radius.mp3'];
var soundResourceBase = './sound/'; // 2025.7.5 音效资源的基础路径，末尾要带上 '/'
var servers = {
	server1: {
		name: '自由♂美利坚',
		ws: 'ws://127.0.0.1:9681', // wss 当然也可以，对应服务端 config/config.php 的 wsport 配置
		core: 'http://127.0.0.1:9680/core/'
	},
}
var defaultserver = 'server1';