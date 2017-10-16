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

use payment\alipay\Alipay;
use payment\alipay\AliPayHelper;
use payment\alipay\AlipayOpenCommon;
use payment\wxpay\WxPay;
use think\Log;
use think\Page;

/**
 * 用户逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class UserLogic extends BaseLogic{
    protected $table = 'ruit_users';

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 获取调起充值支付参数
     * @param $reqParams
     */
    public function getRechargeParams($reqParams, $loginUser){

        $orderSn = "RC{$loginUser['user_id']}".date('YmdHis').rand(10000, 99999); // 订单编号
        $paymentParams = [
            'orderSn' => $orderSn,
            'amount' => $reqParams['amount'],
            'extend' => urlencode(json_encode([
                'userId' => $loginUser['user_id'],
                'amount' => $reqParams['amount'],
                'orderSn' => $orderSn
            ]))
        ];
        trace($paymentParams);

        $result = [];
        if($reqParams['payWay'] == 'zfb'){
            $aliPayHelper = new AliPayHelper();
            $aliPayHelper = new AliPayPayParams();
            $aliPayParams = $aliPayHelper->aliPay($paymentParams);
            $result = ['aliPayParams' => $aliPayParams];
            //$result = ['aliPayParams' => 'app_id=2017061607503256&biz_content={"body":"17051335257\u5145\u503c","subject":"\u5145\u503c","out_trade_no":"2017091652991025","timeout_express":"90m","total_amount":"0.01","product_code":"QUICK_MSECURITY_PAY","passback_params":"recharge"}&charset=UTF-8&format=json&method=alipay.trade.app.pay&notify_url=http://wztx.shp.api.zenmechi.cc/callback/alipay_callback&sign_type=RSA2&timestamp=2017-09-16 13:36:36&version=1.0&sign=C3CYMHfeoPjxpg947K25PF76Y7Q5BhA6dI6vdu+iPH2bfaRBkmIExVVG8b2/UqkhNRpY1hO5BJuim5hOrpi003kMIyb9yfao6MyJyQ6LVIqbcNlRTJOAameTqJe6O4oo7jeOq9X+lTWGH+wVwQi3Oz3eiS892ilBUJPnubtwrTpvT/to74M43/mAy5UtdCkiS6XkkO4PmW2YqTHj5/roB6JzbCqOxlPUsZhqlxssS3jyMM8Id1x3yeuP5o65NX5BB73ivSPh03HdV/E3PgtnG7Ni0KVzRUXJdIr2ez4AHh4h80QW8PecZ4aCIA7wGnLrrkkBViOx9S3WQIYhpiy6AQ=='];
        }elseif($reqParams['payWay'] == 'wx'){
            $wxPayParams = $this->wxPay($paymentParams);
            $result = ['wxPayParams' => $wxPayParams];
        }else{
            return resultArray(4000, '不支持的支付方式');
        }
        return resultArray(2000, '', $result);
    }


    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 充值操作
     */
    public function doRecharge($userId, $amount, $orderSn){
        $user = $this->where('user_id', $userId)->find();
        if(empty($user)){
            trace('充值失败,无效的用户id='.$userId, Log::ERROR);
            return false;
        }
        $user->user_money += $amount;
        $user->save();

        $accountLogLogic = new AccountLogLogic();
        $data = [
            'user_id' => $userId,
            'user_money' => $amount,
            'user_balance' => $user->user_money,
            'change_time' => time(),
            'desc' => '充值',
            'order_sn' => $orderSn,
            'type' => AccountLogLogic::TYPE_RECHANGE,
        ];
        return $accountLogLogic->create($data);

    }

    /** todo 联调成功后 优化代码
     * Author: WILL<314112362@qq.com>
     * Describe: 支付宝支付
     * @param $paymentParams
     * @return array|bool|string
     */
    private function aliPay($paymentParams){
        //dd($paymentParams['amount']);
        $bizContentArr = [
            "subject" => 'FIXME睿途科技',
            "out_trade_no" => $paymentParams['orderSn'],
            "timeout_express" => '90m',
            "total_amount" => $paymentParams['amount'],
            "product_code" => "QUICK_MSECURITY_PAY",
            "passback_params" => $paymentParams['extend'], //$paymentParams['extend'],

        ];
        $bizContent = json_encode($bizContentArr);
        //dd($bizContent);

        $config = [
            'appid' => '2017101309278810',
            'alipay_account' => '',
            'alipay_key' => '',
            'alipay_partner' => '',
            'alipay_private_key' => 'MIICWwIBAAKBgQCp5Vr4s2QSo75N6lTM94lLaU7dwWt9TpWkI2xjzsu9Yteydfx7JGFNH8ox6YRTiL0ieX4Gy+ADXEnyIe0tH0/M9JIXrNcjbWXcAnX203c/arg8UhEppOrse20HsB6UnAkzQe7PhBo8IJBlMypbgQ/Gtyg5jpBCQGJopALLmDjNuwIDAQABAoGAcOht9s4xeXm8pUdQKA9x/N31fcZnSxWIuVaZWBM8r5Yaxw1GSAg7aefwlg8c0+8R0vFu4dFoBJO2yOXnG4+tlfBJjFNsTR8O7aUcKZTHY8WwPG1LAnzBTdhb2QEpqrKnW0FGDSqdn3pkyUGGFNOQekkAnPI+5iWBKpVVUANuk0ECQQDePMNeSMTkPhpfzZSTQdurrgP+MRRR4kdWHV2ThXH9nN9u6qWJSlYuefnaX3SOmvUeVOkgMkwk6Uh2BMNmniCLAkEAw7Tu8e5BgN4Ljcr259YqqrbudW3MIsdQ7mEjDu8vvHJJu/3EMk4m6BUCvpBkkZVJ0uF01TqQLD2VXhWzIF39kQJAUop08Y7Leg5K02xSk4LxaMk/+GgYKPWHE0fi2ojEtrJOFi8WfnBao/ZvwxiS/Og2xCY7U9b6ivcOD69jN7jhUwJAFmDsKdH6v4ekfpRE44HD4z2Dyv3BKADuqjOFVMHZDY1sDQkZlD8Hh+ZPKAdO2K7fgDOzrmZB8i0glV+Z2gulYQJAaa8QNCn6dIl2GLbYOeQVeUXuBXpONAfK0sz/h32pvsUQ2ph29I8RnxbHHcx0k21UDj8fFlUBJfu1KRDk8PX2OQ==',
            'alipay_pay_method' => '2',
            'is_bank' => '1',
        ];
        // $config = unserialize($plugin['config_value']);
        // exit(serialize($config));
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
        $url = U("Api/Payment/alipayNotify", '', false, true);
        $request->setNotifyUrl($url);
        $request->setApiMethodName("alipay.trade.app.pay");

        $request->setBizContent($bizContent);
        $urlParams = $alipay->execute($request);
        return $urlParams;
    }

    /**todo 联调成功后 优化代码
     * Author: WILL<314112362@qq.com>
     * Describe: 微信支付
     * @param $paymentParams
     */
    private function wxPay($paymentParams){
        $order["order_id"] = $paymentParams['orderSn'];
        $order["wxbody"] = 'FIXME 睿途科技';
        $order["attach"] = json_encode($paymentParams['extend']);
        $wxPay = new WxPay();
        return $wxPay->dopay($order);
    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe: 根据用户
     * @param $reqParams
     * @param $user
     *
     * @success {number} list.id.
     * @success {number} list.type 类型.
     * @success {string} list.typeName 类型名称.
     * @success {number} list.timeStamp 变动时间戳.
     * @success {string} list.timeFmt 格式化的变动时间.
     * @success {string} list.changeMoney   变动金额 带+/-.
     * @success {string} list.userBalance   用户余额.
     * @success {string} list.remark   备注.
     * @success {string} list.orderSn   订单号.
     */
    public function getUserAccountLogPageByTimeAndType($reqParams, $user){
        $accountLogLogic = new AccountLogLogic();
        $fields = [
            'log_id' => 'id',
            'type' => 'type',
            'change_time' => 'timeStamp',
            'user_money' => 'changeMoney',
            'user_balance' => 'userBalance',
            'desc' => 'remark',
            'order_sn' => 'orderSn',
        ];
        $where = ['user_id' => $user['user_id']];

        if(!empty($reqParams['type'])){
            $where['type'] = $reqParams['type'];
        }
        if(!empty($reqParams['startTime'])){
            $where['change_time'] = ['BETWEEN', "$reqParams[startTime],$reqParams[endTime]"];
        }
        $count = $accountLogLogic->where($where)->count();
        if(empty($count)){
            $ret = [
                'p' => 1,
                'totalPages' => 0,
                'list' => [],
            ];
            return resultArray(2000, '', $ret);
        }

        $page = new Page($count, 10);
        $list = $accountLogLogic->where($where)
            ->limit($page->firstRow, $page->listRows)
            ->order('change_time DESC')
            ->field($fields)
            ->select();
        $retList = [];
        foreach($list as $item){
            $item['typeName'] = AccountLogLogic::TYPE_ARR[$item['type']];
            $item['timeFmt'] = date('Y.m.d', $item['timeStamp']);
            $item['changeMoney'] = $item['changeMoney'] > 0 ? "+$item[changeMoney]" : $item['changeMoney'];
            $ksortItem = $item->toArray();
            ksort($ksortItem);
            $retList [] = $ksortItem;
        }

        $ret = [
            'p' => $page->nowPage,
            'totalPages' => $page->totalPages,
            'list' => $retList,
        ];
        return resultArray(2000, '', $ret);


    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:
     * @param $user
     * @param $userId
     *
     * avatar      头像.
     * nickname    昵称.
     * sex      性别 0=保密 1=男 2=女.
     * level    等级.
     * fansNum    粉丝数量.
     * attentionNum    关注数量.
     * praiseNum    被赞数量.
     * collectNum    被收藏数量.
     */
    public static function getBaseInfo($user, $userId = 0, $isAnonymous = 0){
        if($isAnonymous){
            $user['nickname'] = hidMiddleStr($user['nickname']);
        }
        $baseInfo = [
            'avatar' => empty($user['head_pic']) ? config('APP_DEFAULT_USER_AVATAR') : $user['head_pic'],
            'nickname' => $user['nickname'],
            'sex' => $user['sex'],
            'level' => $user['level'],
            'fansNum' => $user['attention_num'],
            'attentionNum' => $user['attention_num'],
            'praiseNum' => $user['good_num'],
            'collectNum' => $user['collection_num'],
        ];
        return resultArray(2000, '', $baseInfo);

    }

    /**
     * Author: WILL<314112362@qq.com>
     * Describe:
     * @param $user
     * @param $userId
     *
     * avatar      头像.
     * nickname    昵称.
     * sex      性别 0=保密 1=男 2=女.
     * level    等级.
     * fansNum    粉丝数量.
     * attentionNum    关注数量.
     * praiseNum    被赞数量.
     * collectNum    被收藏数量.
     */
    public static function getBaseInfoById($userId, $viewerId = 0, $isAnonymous = 0){
        if(empty($userId)){
            return resultArray(4004);
        }
        $baseFields = ['head_pic', 'nickname', 'sex', 'level', 'attention_num', 'good_num', 'collection_num'];
        $user = self::field($baseFields)->find($userId);
        return self::getBaseInfo($user, $viewerId, $isAnonymous);

    }

}