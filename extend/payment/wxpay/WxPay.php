<?php

namespace payment\wxpay;

//require_once("WxPayConfig.php");
//require_once("WxPayNotify.php");
//require_once("WxPayReport.php");
//require_once("WxPayResults.php");
//require_once("WxPayJsData.php");

class WxPay{

    /**
     * 统一下单
     */
    public function dopay($order, $tradeType = "APP"){
        header('Access-Control-Allow-Origin: *');
        header('Content-type: text/plain');

        // 获取支付金额
        $amount = $order['amount'];
        $total = floatval($amount);
        $total = round($total*100); // 将元转成分
        if(empty($total)){
            $total = 100;
        }
        // 订单号，示例代码使用时间值作为唯一的订单ID号
        $unifiedOrder = new WxPayUnifiedOrder();
        $WxPayApi = new WxPayApi();

        if($tradeType == "JSAPI"){
            $WxPayConfig = WxPayConfig::getInstance("weixin");
            $unifiedOrder->SetOpenid(session('openId'));
        }else{
            $WxPayConfig = WxPayConfig::getInstance("appWeixinPay"); //appWeixinPay
            $tradeType = "APP";
        }

        $unifiedOrder->SetBody($order["wxbody"]);//商品或支付单简要描述
        //$unifiedOrder->SetAppid($WxPayConfig::$APPID);//appid
        $unifiedOrder->SetAppid('wx444bb74a6d803478');//appid
        $unifiedOrder->SetMch_id('1489687802');//商户标识
        $unifiedOrder->SetNonce_str($WxPayApi::getNonceStr($length = 32));//随机字符串
        $unifiedOrder->SetDetail($order["detail"]);//详情
        $unifiedOrder->SetOut_trade_no($order["order_id"]);//交易号
        $unifiedOrder->SetTotal_fee($total);//交易金额
        $unifiedOrder->SetTrade_type($tradeType);//应用类型
        $unifiedOrder->SetAttach($order["attach"]);//应用类型
        $unifiedOrder->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//发起充值的ip
        $url = U("Api/Payment/wxpayNotify", '', false, true);
        $unifiedOrder->SetNotify_url($url);//交易成功通知url
        //$unifiedOrder->SetTrade_type($native);//支付类型
        $unifiedOrder->SetProduct_id(time());

        $wxPayCnf = $WxPayApi::unifiedOrder($unifiedOrder);
        $jsApiParameters = $this->GetJsApiParameters($wxPayCnf);
        $wxPayCnf["jsConfig"] = $jsApiParameters;
        return $wxPayCnf;
    }


    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult){
        if(!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("partnerid", $UnifiedOrderResult) || $UnifiedOrderResult['partnerid'] == ""){
            return "参数错误";
        }
        $WxPayApi = new WxPayApi();
        $jsapi = new WxPayJsData();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr($WxPayApi::getNonceStr($length = 32));
        $jsapi->SetPackage("prepay_id=".$UnifiedOrderResult['prepayid']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->SetSign());
        $parameters = $jsapi->GetValues();
        return $parameters;
    }


}