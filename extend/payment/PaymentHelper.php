<?php

namespace payment;


use payment\alipay\Alipay;
use payment\alipay\AlipayConfig;
use payment\alipay\AlipayOpenCommon;
use payment\wxpay\WxPay;

class PaymentHelper{

    const PARAM_FORMAT_JSON       = 1; //返回参数格式
    const PARAM_FORMAT_URL_ENCODE = 2; //返回参数格式

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:
     * @param $paymentParams
     */
    public function getAliPayParam(PaymentBizParam $paymentParams, $format = self::PARAM_FORMAT_URL_ENCODE){
        //dd($paymentParams['amount']);
        $bizContentArr = [
            "subject" => '傻孩子',
            "out_trade_no" => $paymentParams->orderSn,
            "timeout_express" => '90m',
            "total_amount" => $paymentParams->amount,
            "product_code" => "QUICK_MSECURITY_PAY",
            "passback_params" => $paymentParams->extend, //$paymentParams['extend'],

        ];
        $bizContent = json_encode($bizContentArr);

        // $config = unserialize($plugin['config_value']);
        // exit(serialize($config));
        if(empty(AlipayConfig::APPID) || empty(AlipayConfig::APP_PRIVATE_KEY)){
            return resultArray(4000, '没有配置支付宝支付参数');
        }

        $alipay = new Alipay;
        $alipay->rsaPrivateKeyFilePath = null;
        $alipay->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $alipay->appId = AlipayConfig::APPID; // 2017052207306091
        $alipay->rsaPrivateKey = AlipayConfig::APP_PRIVATE_KEY;  // MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALOAicUPRZ0Hru8w43A7DIEZjVSrqTrRJoaYojr5hXgdsEobqeFCL31alKqz8KMtS9gxO2gkZtBsj1GsajYFiIrz3FUAeOSh6xxPOZCS82aqIxGmeBUUUcHtgvS2dyIva1Zt9S6vdBF4TNWFE2m9tvrqfENsUjoN6HdBdPIkD8+3AgMBAAECgYAsxreXLIQU88GzcOKLMG+iFJmosVl5joqpsJFnXK7qk51SHyx1QGlQP7QuEMzKJ5Zvy3giNlJfU3U8zmGAMEkq1ONS08/JVmLMndLxiRaWfnES76eUz01Y6ZxZC4YpaWsxzDleVrh2h57rRb63qiRhXLdNi5GrJw0DMQKgN/YCYQJBAOpI4Lhjjv59+xbehxya5MMUgstbZf2YQZwVf90V7P46QqVxKsLd09mHyoliCSM6IlhB89r406TU6vl/y006dYsCQQDEI8aZ2N8IBSq7NmdrEau6dzQ1NUsS7r1n9RaE1NJ7WF0ECNRhfMpNkZJ1PcnyThA9J8wb6n0i+3XDMzTUWwwFAkAzy8Lq4Q/nEcEmUDI8z73Np0Y3YVCOHVA8CsDHBybrGcRMQVW72UER8aSEdQkiIaMgMgyQl7xqz6vXVzqCK297AkBLQ18mEe4jabgn9oxgrXs0JiHGeRjBvxK3HXjyp6fM5O9saOb2Mah/c2i7zGX9sK7SiL7tx2EVV2Cs8q1G/1jxAkEAsQCC62m1yS0ije/gBwfOx7M9U3J8a1vpxPzH408TprtOz4xxGAjZU7D05v6FQUi/g5Z7LhFQaiVfuQXyPDpGDg==
        $alipay->format = "json";
        $alipay->charset = "UTF-8";
        $request = new AlipayOpenCommon();
        $url = U("Api/Payment/alipayNotify", '', false, true);
        $request->setNotifyUrl($url);
        $request->setApiMethodName("alipay.trade.app.pay");
        $request->setBizContent($bizContent);
        $urlParams = $alipay->execute($request);
        return $urlParams;
    }

    public function getWxPayParam(PaymentBizParam $payparam, $format = self::PARAM_FORMAT_JSON){
        $order["wxbody"] = '傻孩子';
        $order["order_id"] = $payparam->orderSn;
        $order["amount"] = $payparam->amount;
        $order["attach"] = $payparam->extend;
        $wxPay = new WxPay();
        return $wxPay->dopay($order);
    }


}