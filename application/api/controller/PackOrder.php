<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 14:48
 */

namespace app\api\controller;

use app\common\logic\CouponListLogic;
use app\common\logic\PackOrderLogic;
use payment\PaymentBizParam;
use payment\PaymentHelper;
use think\Request;

class PackOrder extends Base{

    public $packLogic;

    function __construct(){
        parent::__construct();
        $this->packLogic = new PackOrderLogic();
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=PackOrder&a=getPackOrder  得到包车订单列表done 管少秋
     * @apiName     getPackOrder
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token.
     * @apiParam    {String=ALL,UN_PAY,DOING,UN_COMMENT,FINISH}  status=ALL  订单状态
     * @apiSuccessExample   {json}  Success-Response
     * Http/1.1     200 OK
     * {
     * "status": 1,
     * "msg": "成功",
     * "result": {
     * "totalPages": 1,
     * "list": [
     * {
     * “air_id” : 20,
     * "order_sn": "201709091232",
     * "seller_id": 19,
     * "status": 5,
     * "title": "自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返",
     * "customer_name": "西班牙",
     * "drv_name": "醉生梦死",
     * "create_at": 1504858382,
     * "drv_phone": null,
     * "total_price": 100,
     * "real_price": "100.00"
     * },
     * {
     * “air_id” : 20,
     * "order_sn": "201709091232",
     * "seller_id": 20,
     * "status": 0,
     * "title": "自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返",
     * "customer_name": "西班牙",
     * "drv_name": "醉生梦死",
     * "create_at": 1504858382,
     * "drv_phone": null,
     * "total_price": 100,
     * "real_price": "100.00"
     * "seller_order_status": "1"//司导的订单状态 0是待评价 1是已评价
     * "user_order_status": "0"//用户的订单状态 0是待评价 1是已评价
     * },
     * ]
     * }
     * }
     */
    public function getPackOrder(){
        $status = input('status', 'ALL');
        $result = $this->packLogic->get_pack_order($status, $this->user_id);
        $this->ajaxReturn($result);
    }

    /**
     * @api         {POST}   /index.php?m=Api&c=PackOrder&a=getPackOrderDetail    得到包车订单详情done 管少秋
     * @apiName     getPackOrderDetail
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    air_id  订单ID
     * @apiSuccessExample   {json}  Success-Response
     * Http/1.1     200 OK
     * "result": {
     * "air_id": 16,
     * "order_sn": "201709091232",
     * "user_id": 60,
     * "seller_id": 19,
     * "allot_seller_id": ",18,19,20,",
     * "customer_name": "西班牙",
     * "customer_phone": 1322222222,
     * "use_car_adult": 10,
     * "use_car_children": null,
     * "work_at": 22,
     * "work_pointlng": 123.021,
     * "work_pointlat": 36.25,
     * "work_address": "江苏省苏州市",
     * "dest_pointlng": 125.236,
     * "dest_pointlat": 36.23,
     * "dest_address": "英格兰",
     * "status": 5,  //订单状态 0未支付 1待派单 2待接单 3进行中（待开始、待确认） 5待评价 6已完成
     * "pay_way": 1,
     * "total_price": 100,
     * "real_price": "100.00",
     * "is_pay": 1,
     * "pay_time": 1505119625,
     * "start_time": 1508688000,
     * "end_time": 1505119723,
     * "add_time_long": null,
     * "add_recharge": null,
     * "add_reason": null,
     * "drv_name": "醉生梦死",
     * "drv_id": 3,
     * "drv_code": "121540215",
     * "req_car_id": 11245,
     * "req_car_type": "大众桑塔纳",
     * "con_car_id": 1,
     * "con_car_type": "2",
     * "con_car_seat_num": null,
     * "type": 1,  // 1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游
     * "flt_no": "",
     * "mile_length": 100,
     * "discount_id": 23,
     * "user_message": "",
     * "create_at": 1504858382,
     * "update_at": 1504858382,
     * "title": "自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返",
     * "is_use_car": 1,
     * "remark": null,
     * "drv_phone": null
     * }
     */
    public function getPackOrderDetail(){
        $air_id = I('air_id');
        $result = $this->packLogic->get_pack_order_info($air_id, $this->user_id);
        $this->ajaxReturn($result);
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=PackOrder&a=payPackOrder    订单支付 todo
     * @apiName     payPackOrder
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token
     * @apiParam    {Number}    air_id  订单ID
     * @apiParam    {Number}    pay_way    支付方式 0微信支付 1支付宝支付 2余额支付
     * @apiParam    {Float}    [real_price]   优惠的价格
     * @apiParam    {Number}    [coupon_id]   优惠券ID 无优惠券传空进来
     */
    public function payPackOrder(){
        //判断订单是否在有效期内 有效付款时间24小时
        //判断用户
        $clLogic = new CouponListLogic();
        $air_id = I('air_id');
        $pack_order = M('pack_order')
            ->where(array('air_id' => $air_id, 'status' => PackOrderLogic::STATUS_UNPAY))
            ->find();//订单详情
        $time = time();
        //print_r($pack_order);die;
        if(empty($pack_order)){
            $this->ajaxReturn(['status' => -1, 'msg' => '该订单不可进行付款操作']);
        }
        if($time - $pack_order['create_at'] > 86400){//超过一天
            $this->ajaxReturn(['status' => -1, 'msg' => '该订单已超时']);
        }
        $coupon_id = I('coupon_id');
        $pay_way = I('pay_way');//获取支付方式
        $discount_price = 0;//优惠价格
        $is_coupon = false;//优惠券是否可以用
        if(!empty($coupon_id)){
            //通过优惠券去判断需要优惠的价格
            $where = [
                'l.id' => $coupon_id,
                'l.uid' => $this->user_id,
                'l.model_type' => 0, //代表包车订单
                'c.condition' => ['lt', $pack_order['total_price']],//订单金额 要 大于优惠券的条件
                'c.status' => 1,//是否可使用
                'l.status' => 0,
                'c.use_start_time' => ['lt', $time],//是否开始 开始时间小于当前时间
                'c.use_end_time' => ['gt', $time]//是否过期    过期时间大于当前时间
            ];

            $coupon_info = $clLogic->alias('l')
                ->join('ruit_coupon c', 'l.cid = c.id')
                ->where($where)
                ->field('l.id,c.*')
                ->find();//找出优惠券
            //echo $coupon_info;die;->fetchSql(true)
            if(empty($coupon_info)){
                $this->ajaxReturn(['status' => -1, 'msg' => '该优惠券不满足条件']);
            }
            $is_coupon = true;//优惠券可以用
            $discount_price = $coupon_info['money'];
        }
        $real_price = $pack_order['total_price'] - $discount_price;//真实价格
        $user_info = M('users')->where('user_id', $this->user_id)->find();
        if($pay_way == 0){  //todo 微信支付
            return $this->returnJson(4000,'暂未开放');
        }elseif($pay_way == 1){ //todo 进行支付宝支付

            $alipayHelper = new PaymentHelper();
            //传递需要通过服务器
            $aliPayParams = new PaymentBizParam($pack_order['order_sn'],$real_price,'');
            $payString = $alipayHelper->getAliPayParam($aliPayParams);
            if(empty($payString)){
                return $this->returnJson(4004);
            }
            $ret=['aliPayParams'=>$payString];
            //$result = payPackOrder($pack_order, $user_info, $discount_price, $pay_way, $is_coupon, $coupon_id);
            $ret['realPrice'] = shzMoney($real_price,true);
            return $this->returnJson(2000,'',$ret);
        }elseif($pay_way == 2){// 余额支付
            //print_r($this->user_id);die;
            //进行付款操作----------》
            ($user_info['user_money'] - $real_price) < 0 && $this->ajaxReturn([
                'status' => -1,
                'msg' => '抱歉，您的账户余额不足，请使用其他方式付款'
            ]);
            //订单信息,用户信息,优惠价格,支付方式,优惠券ID,优惠券是否可以使用
            $result = payPackOrder($pack_order, $user_info, $discount_price, $pay_way, $is_coupon, $coupon_id);
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn(['status' => -1, 'msg' => '不支持的支付方式']);
        }
    }

    /**
     * @api         {POST}  /index.php?m=Api&c=PackOrder&a=createPackOrder      生成订单，路线订单，其他订单后台生成done 管少秋
     * @apiName     createPackOrder
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token
     * @apiParam    {Number}    line_id     路线ID
     * @apiParam    {Number}    title     路线标题
     * @apiParam    {String}    customer_name     顾客名称
     * @apiParam    {String}    customer_phone     客户的手机号
     * @apiParam    {String}    user_passport      用户护照
     * @apiParam    {String}    user_identity      用户的身份证
     * @apiParam    {String}    [twenty_four]      行李箱尺寸
     * @apiParam    {String}    [twenty_six]      行李箱尺寸
     * @apiParam    {String}    [twenty_eight]      行李箱尺寸
     * @apiParam    {String}    [thirty]      行李箱尺寸
     * @apiParam    {Number}    use_car_adult     成人用车人数
     * @apiParam    {Number}    use_car_children     儿童人数
     * @apiParam    {String}    work_at     服务日期
     * @apiParam    {Number}    [discount_id]     优惠券ID
     * @apiParam    {String}    total_price     订单总价格
     * @apiParam    {String}    real_price     优惠后，实际支付的价格
     * @apiParam    {String}    [user_message]     用户留言
     *
     */
    public function createPackOrder(){
        $data = I('post.');
        $result = $this->validate($data, 'PackOrder');
        if($result === true){//验证通过
            //创建订单
            $result = $this->packLogic->create_pack_order($data, $this->user);
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn(['status' => -1, 'msg' => $result]);
        }
    }

    /**
     * @api         {PUT}  /index.php?m=Api&c=PackOrder&a=confirmFinish   确认结束订单 ok wxx
     * @apiName     confirmFinish
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token
     * @apiParam    {Number}    id  订单ID
     */
    public function confirmFinish(Request $request){
        if(!$request->isPut()){
            $this->returnJson(4000);
        }
        $id = I('id');
        $orderLogic = new PackOrderLogic();
        $order = $orderLogic->find($id);
        if(empty($order)){
            $this->returnJson(4004);
        }
        if($order->user_id != $this->user_id){
            $this->returnJson(4010);
        }
        if($order->status == PackOrderLogic::STATUS_UNCOMMENT){
            $this->returnJson(4005);
        }
        $this->returnJson($orderLogic->confirmFinish($order, $this->user));


    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe:
     * @param $pack_order
     */
    public function getAliPayParamsByPackOrder($pack_order){
    }
}