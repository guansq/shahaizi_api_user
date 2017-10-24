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
use app\common\logic\RechargeLogic;
use think\Request;

class Payment extends Base{
    /**
     * app端发起支付宝,支付宝返回服务器端,  返回到这里
     * http://www.tp-shop.cn/index.php/Api/Payment/alipayNotify
     *
     */
    public function alipayNotify(Request $request){
        if(!$request->isPost()){
            exit("fail");
        }
        $resp = I("post.");
        trace('支付宝回调========》');
        trace($resp);
        $paymentPlugin = M('Plugin')->where("code='alipayMobile' and  type = 'payment' ")->find(); // 找到支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化
        $alipay_config['partner'] = $config_value['alipay_partner'];//合作身份者id，以2088开头的16位纯数字

        require_once("plugins/payment/alipay/app_notify/alipay.config.php");
        require_once("plugins/payment/alipay/app_notify/lib/alipay_notify.class.php");

        //计算得出通知验证结果
        //$alipayNotify = new AlipayNotify($alipay_config);

        //todo 验证失败
        //if(!$alipayNotify->verifyNotify($resp)){
        //    trace('验证失败');
        //    exit("fail"); //验证失败
        //}

        $orderSn = $out_trade_no = trim($resp['out_trade_no']); //商户订单号
        $tradeNo = $resp['trade_no'];//支付宝交易号
        $trade_status = $resp['trade_status'];//交易状态

        //用户支付失败
        if($resp["trade_status"] != "TRADE_SUCCESS"){
            exit("fail");
        }
        // $resp['body'] ='{"userId":63,"amount":1,"orderSn":"RC201723443234565432345432"}';
        $extend = json_decode(urldecode($resp['passback_params']), true);
        // 用户充值 充值订单号是RC开头
        if(substr($orderSn, 0, 2) == 'RC'){
            // 充值逻辑

            if(empty($extend)){
                exit("fail");
            }

            $userLogic = new UserLogic();
            trace("用户充值 =========》");
            trace($extend);
            if($userLogic->doRecharge($extend['userId'], $extend['amount'], $orderSn, 'alipay','支付宝',$tradeNo,'')){
                exit("success");
            }else{
                exit("fail");
            };
        }

        //todo 其他支付回到处理

        payPackOrder($extend['pack_order'], $extend['user_info'], $extend['discount_price'], $extend['pay_way'], $extend['is_coupon'], $extend['coupon_id']);

        exit("fail");
    }


    /**
     * 微信支付通知
     */
    public function wxpayNotify(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml = $xml ?: file_get_contents('php://input');
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        trace("微信支付回调==============》");
        trace($result);

        header('Content-Type:text/xml; charset=utf-8');

        if($result['return_code'] == 'SUCCESS'){
            $orderSn = $result['out_trade_no'];
            $tradeNo = $result['transaction_id'];
            $wx_total_fee = $result['total_fee'];
            // 用户充值 充值订单号是RC开头
            if(substr($orderSn, 0, 2) == 'RC'){
                // 充值逻辑
                // $resp['body'] ='{"userId":63,"amount":1,"orderSn":"RC201723443234565432345432"}';
                $extend = json_decode(urldecode($result['attach']), true);
                if(empty($extend)){
                    trace("attach格式错误");
                    $test = ['return_code' => 'FAIL', 'return_msg' => 'attach格式错误'];
                    exit(arrayToXml($test));
                }

                $userLogic = new UserLogic();
                trace("用户充值 =========》");
                trace($extend);
                if($userLogic->doRecharge($extend['userId'], $extend['amount'], $orderSn,'wx','微信',$tradeNo,'')){
                    trace("用户充值成功");
                    $test = ['return_code' => 'SUCCESS', 'return_msg' => 'SUCCESS'];
                    exit(arrayToXml($test));
                }else{
                    trace("用户充值失败");
                    $test = ['return_code' => 'FAIL', 'return_msg' => '用户充值失败'];
                    exit(arrayToXml($test));
                }
            }

            //todo 其他支付回到处理

        }
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
