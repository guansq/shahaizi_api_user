<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\api\logic;

use app\common\logic\UsersLogic as CommonUserLogic;
use payment\alipay\Alipay;
use payment\alipay\AlipayOpenCommon;


/**
 * 用户逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class UserLogic extends CommonUserLogic{

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:
     * @param $reqParams
     */
    public function getRechargeParams($reqParams, $loginUser){

        $orderSn = "RC$loginUser[user_id]$reqParams[amount]".date('YmdHis').rand(10000, 99999); // 订单编号
        $paymentParams = [
            'orderSn' => $orderSn,
            'amount' => $reqParams['amount'],
            'extend' => [
                'userId' => $loginUser['user_id'],
                'amount' => $reqParams['amount'],
                'orderSn' => $orderSn
            ]
        ];

        $result = [];
        if($reqParams['payWay'] == 'zfb'){
            // todo 传参
            $aliPayParams = $this->aliPay($paymentParams);
            $result =['aliPayParams'=>$aliPayParams];
        }elseif($reqParams['payWay'] == 'wx'){
            $result = $this->wxPay($paymentParams);
        }

        return resultArray(2000, '', $result);
    }


    public function aliPay($paymentParams){

        $bizContentArr = [
            "subject" => 'TODO睿途科技/流量达人',
            "out_trade_no" => $paymentParams['orderSn'],
            "product_code" => "QUICK_MSECURITY_PAY",
            "timeout_express" => '90m',
            "total_amount" => $paymentParams['amount'],
            "body" => [$paymentParams['extend']]
        ];

        $bizContent = json_encode($bizContentArr);


        $plugin = M('plugin')->where(array('type' => 'payment', 'code' => 'alipayMobile'))->find();
        if(!$plugin){
            return resultArray(4000, '没有手机支付宝插件');
        }
        $config = unserialize($plugin['config_value']);
        if(empty($config['appid']) || empty($config['alipay_private_key'])){
            return resultArray(4000, '没有手机支付宝插件参数');
        }
        $alipay = new Alipay;
        $alipay->rsaPrivateKeyFilePath = null;
        $alipay->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $alipay->appId = $config['appid']; // 2017052207306091
        $alipay->rsaPrivateKey = $config['alipay_private_key'];  // MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALOAicUPRZ0Hru8w43A7DIEZjVSrqTrRJoaYojr5hXgdsEobqeFCL31alKqz8KMtS9gxO2gkZtBsj1GsajYFiIrz3FUAeOSh6xxPOZCS82aqIxGmeBUUUcHtgvS2dyIva1Zt9S6vdBF4TNWFE2m9tvrqfENsUjoN6HdBdPIkD8+3AgMBAAECgYAsxreXLIQU88GzcOKLMG+iFJmosVl5joqpsJFnXK7qk51SHyx1QGlQP7QuEMzKJ5Zvy3giNlJfU3U8zmGAMEkq1ONS08/JVmLMndLxiRaWfnES76eUz01Y6ZxZC4YpaWsxzDleVrh2h57rRb63qiRhXLdNi5GrJw0DMQKgN/YCYQJBAOpI4Lhjjv59+xbehxya5MMUgstbZf2YQZwVf90V7P46QqVxKsLd09mHyoliCSM6IlhB89r406TU6vl/y006dYsCQQDEI8aZ2N8IBSq7NmdrEau6dzQ1NUsS7r1n9RaE1NJ7WF0ECNRhfMpNkZJ1PcnyThA9J8wb6n0i+3XDMzTUWwwFAkAzy8Lq4Q/nEcEmUDI8z73Np0Y3YVCOHVA8CsDHBybrGcRMQVW72UER8aSEdQkiIaMgMgyQl7xqz6vXVzqCK297AkBLQ18mEe4jabgn9oxgrXs0JiHGeRjBvxK3HXjyp6fM5O9saOb2Mah/c2i7zGX9sK7SiL7tx2EVV2Cs8q1G/1jxAkEAsQCC62m1yS0ije/gBwfOx7M9U3J8a1vpxPzH408TprtOz4xxGAjZU7D05v6FQUi/g5Z7LhFQaiVfuQXyPDpGDg==
        $alipay->format = "json";
        $alipay->charset = "UTF-8";
        $request = new AlipayOpenCommon();
        $url = U("Api/Callback/alipay", '', false, true);
        $request->setNotifyUrl($url);
        $request->setApiMethodName("alipay.trade.app.pay");

        $request->setBizContent($bizContent);
        $urlParams = $alipay->execute($request);
        return $urlParams;
    }

}