<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */

namespace app\api\controller;

use app\api\logic\UserLogic;
use think\Request;

class Payment extends Base{
    /**
     * app端发起支付宝,支付宝返回服务器端,  返回到这里
     * http://www.tp-shop.cn/index.php/Api/Payment/alipayNotify
     *
     * DEBUG: 支付宝支付回调===》
    DEBUG: Array
    (
    [gmt_create] => 2017-08-26 11:28:12
    [charset] => UTF-8
    [seller_email] => liuliangdaren@163.com
    [subject] => 梦龙流量商城订单
    [sign] => OmaGWm7nImMwrE7GCF8v0aQ2j0Fn/kNlbYIBGCCFpUy4HByvkwfGykqcpL4iAo9tSqK17xrMZh/x3/phug/fFJ0awcsIc3lksi9RLlKdbiKgIU4hOe5lVQUzIaUSjCKyoozJwz5C3Egq69IaL0D5fwSTk/+UGT8F6UTfvIiuyVs=
    [body] => [{"order_name":"master_order_sn","is_paypoint":0,"user_id":"1013","type":"1"}]
    [buyer_id] => 2088002055168212
    [invoice_amount] => 0.01
    [notify_id] => 3aff1488e77da76d20071ee1912cdf3hme
    [fund_bill_list] => [{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}]
    [notify_type] => trade_status_sync
    [trade_status] => TRADE_SUCCESS
    [receipt_amount] => 0.01
    [app_id] => 2017052207306091
    [buyer_pay_amount] => 0.01
    [sign_type] => RSA
    [seller_id] => 2088721066079239
    [gmt_payment] => 2017-08-26 11:28:13
    [notify_time] => 2017-08-26 11:28:13
    [version] => 1.0
    [out_trade_no] => 201708261127521217
    [total_amount] => 0.01
    [trade_no] => 2017082621001004210278212823
    [auth_app_id] => 2017052207306091
    [buyer_logon_id] => 285***@qq.com
    [point_amount] => 0.00
    [time] => 0
    )
     */
    public function alipayNotify(Request $request ){
        if(!$request->isPost()){
            exit("fail");
        }
        $resp = I("post.");
        $paymentPlugin = M('Plugin')->where("code='alipayMobile' and  type = 'payment' ")->find(); // 找到支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化
        $alipay_config['partner'] = $config_value['alipay_partner'];//合作身份者id，以2088开头的16位纯数字

        require_once("plugins/payment/alipay/app_notify/alipay.config.php");
        require_once("plugins/payment/alipay/app_notify/lib/alipay_notify.class.php");

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);

        //todo 验证失败
        // if(!$alipayNotify->verifyNotify()){
        //     exit("fail"); //验证失败
        // }

        $order_sn = $out_trade_no = trim($resp['out_trade_no']); //商户订单号
        $trade_no = $resp['trade_no'];//支付宝交易号
        $trade_status = $resp['trade_status'];//交易状态

        //用户支付失败
        if($resp["trade_status"] != "TRADE_SUCCESS"){
            exit("fail");
        }

        // 用户充值 充值订单号是RC开头
        if(substr($order_sn, 0,2) == 'RC'){
            // 充值逻辑
            // $resp['body'] ='{"userId":63,"amount":1,"orderSn":"RC201723443234565432345432"}';
            $extend = json_decode($resp['body'],true);
            if(empty($extend)){
                exit("fail");
            }
            $userLogic = new UserLogic();
            if($userLogic->doRecharge($extend['userId'],$extend['amount'],$order_sn)){
                exit("success");
            }else{
                exit("fail");
            };
        }else{
            $order_amount = M('order')
                ->where(['master_order_sn' => $order_sn])
                ->whereOr(['order_sn' => $order_sn])
                ->sum('order_amount');
        }
        if($order_amount != $_POST['price']){
            exit("fail");
        } //验证失败

        if($_POST['trade_status'] == 'TRADE_FINISHED'){
            update_pay_status($order_sn); // 修改订单支付状态
        }elseif($_POST['trade_status'] == 'TRADE_SUCCESS'){
            update_pay_status($order_sn); // 修改订单支付状态
        }
        M('order')
            ->where('order_sn', $order_sn)
            ->whereOr('master_order_sn', $order_sn)
            ->save(array('pay_code' => 'alipay', 'pay_name' => 'app支付宝'));
        exit("success");  //  告诉支付宝支付成功 请不要修改或删除
    }


    /**
     * 微信支付通知
     */
    public function wxpayNotify(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml = $xml ?: file_get_contents('php://input');
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $return_result = 'FAIL';
        file_put_contents('./notify.log', json_encode($result)."\n", FILE_APPEND);
        if($result['return_code'] == 'SUCCESS'){
            $order_sn = substr($result['out_trade_no'], 0, 18);
            $wx_total_fee = $result['total_fee'];
            //用户在线充值
            if(stripos($order_sn, 'recharge') === 0){
                $order_amount = M('recharge')->where(['order_sn' => $order_sn, 'pay_status' => 0])->value('account');
            }else{
                $order_amount = M('order')
                    ->where(['master_order_sn' => "$order_sn"])
                    ->whereOr(['order_sn' => "$order_sn"])
                    ->sum('order_amount');
            }
            file_put_contents('./notify.log', $order_amount."\n", FILE_APPEND);
            if(($order_amount*100) == $wx_total_fee){
                update_pay_status($order_sn);
                $return_result = 'SUCCESS';
            }
        }

        $test = array('return_code' => $return_result, 'return_msg' => 'OK');
        header('Content-Type:text/xml; charset=utf-8');
        exit(arrayToXml($test));
    }


    /*
     * @api {GET}   /index.php?m=Api&c=Payment&a=alipay_sign
     * @apiName     alipay_sign
     * @apiGroup    Pay
     *
     */
    public function alipay_sign(){
        $orderSn = input('post.order_sn', '');
        $user = session('user');

        if(strpos($orderSn, 'recharge') === 0 || $orderSn === ''){
            //充值流程
            $orderAmount = input('account/f', 0);
            if($orderAmount <= 0){
                $this->ajaxReturn(['status' => -1, 'msg' => '充值金额不能为'.$orderAmount]);
            }
            if($orderSn){
                $order = M('recharge')->where("order_sn", $orderSn)->find();
                if(!$order){
                    $this->ajaxReturn(['status' => -1, 'msg' => '该充值订单不存在']);
                }
                M('recharge')
                    ->where(['order_sn' => $orderSn, 'user_id' => $user['user_id']])
                    ->save(['account' => $orderAmount]);
            }else{
                $orderSn = 'recharge'.get_rand_str(10, 0, 1);
                $order['user_id'] = $user['user_id'];
                $order['nickname'] = $user['nickname'];
                $order['account'] = $orderAmount;
                $order['order_sn'] = $orderSn;
                $order['pay_name'] = 'app支付宝';
                $order['ctime'] = time();
                M('recharge')->add($order);
            }
        }else{
            //支付流程
            $order = M('order')
                ->alias('o')
                ->field('o.order_amount')
                ->where('o.order_sn|o.master_order_sn', $orderSn)
                ->select();
            if(!$order){
                $this->ajaxReturn(['status' => -1, 'msg' => '订单不存在']);
            }
            // 所有商品单价相加
            $orderAmount = array_reduce($order, function($sum, $val){
                return $sum + $val['order_amount'];
            }, 0);
        }

        if(!function_exists('openssl_sign')){
            $this->ajaxReturn(['status' => -1, 'msg' => '请先启用php的openssl扩展']);
        }

        $paymentPlugin = M('plugin')->where(['code' => 'alipay', 'type' => 'payment'])->find();
        $cfgVal = unserialize($paymentPlugin['config_value']); // 配置反序列化
        if(!$cfgVal || empty($cfgVal['alipay_partner']) || empty($cfgVal['alipay_private_key']) || empty($cfgVal['alipay_account'])){
            $this->ajaxReturn(['status' => -1, 'msg' => '支付宝重要配置不能为空！']);
        }

        $storeName = M('config')->where('name', 'store_name')->getField('value');

        include_once(PLUGIN_PATH.'payment/alipay/app_notify/lib/alipay_sign.class.php');

        $sign = new \AlipaySign;
        $sign->partner = $cfgVal['alipay_partner'];
        $sign->rsaPrivateKey = $cfgVal['alipay_private_key'];
        $sign->seller_id = $cfgVal['alipay_account'];
        $sign->notifyUrl = SITE_URL.'/index.php/Api/Payment/alipayNotify';
        $result = $sign->execute($storeName, $storeName, $orderAmount, $orderSn);

        $this->ajaxReturn(['status' => 1, 'msg' => '签名成功', 'result' => $result]);
    }

}
