<?php
$home_config = [
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
	//默认错误跳转对应的模板文件
	'dispatch_error_tmpl' => 'public:dispatch_jump',
	//默认成功跳转对应的模板文件
	'dispatch_success_tmpl' => 'public:dispatch_jump', 
	'API_SECRET_KEY'        =>'www.tp-shop.cn', // app 调用的签名秘钥
	'app_access_key'        =>'ncywYGNcJkYKrzUx_ZfY5oz2inkjYHQFl_9UXushZT1LxqY1PE',//傻孩子推送给用户app调用key
	'bus_app_access_key'    =>'si1f1Ir6JL2cNF5a_2zYNJ5CPTaDZeFBe_IjJ5RXP34y2nuPv7',//傻孩子推送给商家app调用key
];

$html_config = include_once 'html.php';
return array_merge($home_config,$html_config);
?>