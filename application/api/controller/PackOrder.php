<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 14:48
 */
namespace app\api\controller;
use app\common\logic\PackOrderLogic;

class PackOrder extends Base{

    public $packLogic;

    function __construct(){
        parent::__construct();
        $this->packLogic = new PackOrderLogic();
    }

    /**
     * @api {POST}   /index.php?m=Api&c=PackOrder&a=getPackOrder  得到包车订单列表done 管少秋
     * @apiName     getPackOrder
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    订单状态 0未支付 1待派单 2待接单 3进行中（待开始、待确认） 4待评价 5已完成 all为全部
     * @apiSuccessExample   {json}  Success-Response
     * Http/1.1     200 OK
    {
    "status": 1,
    "msg": "成功",
    "result": {
        "totalPages": 1,
        "list": [
            {
           “air_id” : 20,
            "order_sn": "201709091232",
            "seller_id": 19,
            "status": 5,
            "title": "自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返",
            "customer_name": "西班牙",
            "drv_name": "醉生梦死",
            "create_at": 1504858382,
            "drv_phone": null,
            "total_price": 100,
            "real_price": "100.00"
            },
            {
            “air_id” : 20,
            "order_sn": "201709091232",
            "seller_id": 20,
            "status": 0,
            "title": "自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返",
            "customer_name": "西班牙",
            "drv_name": "醉生梦死",
            "create_at": 1504858382,
            "drv_phone": null,
            "total_price": 100,
            "real_price": "100.00"
            },
        ]
    }
    }
     */
    public function getPackOrder(){
        $type = I('type',0);
        $result = $this->packLogic->get_pack_order($type,$this->user_id);
        $this->ajaxReturn($result);
    }

    /**
     * @api {POST}   /index.php?m=Api&c=PackOrder&a=getPackOrderDetail    得到包车订单详情done 管少秋
     * @apiName     getPackOrderDetail
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    air_id  订单ID
     * @apiSuccessExample   {json}  Success-Response
     * Http/1.1     200 OK
    "result": {
        "air_id": 16,
        "order_sn": "201709091232",
        "user_id": 60,
        "seller_id": 19,
        "allot_seller_id": ",18,19,20,",
        "customer_name": "西班牙",
        "customer_phone": 1322222222,
        "use_car_adult": 10,
        "use_car_children": null,
        "work_at": 22,
        "work_pointlng": 123.021,
        "work_pointlat": 36.25,
        "work_address": "江苏省苏州市",
        "dest_pointlng": 125.236,
        "dest_pointlat": 36.23,
        "dest_address": "英格兰",
        "status": 5,
        "pay_way": 1,
        "total_price": 100,
        "real_price": "100.00",
        "is_pay": 1,
        "pay_time": 1505119625,
        "start_time": 1508688000,
        "end_time": 1505119723,
        "add_time_long": null,
        "add_recharge": null,
        "add_reason": null,
        "drv_name": "醉生梦死",
        "drv_id": 3,
        "drv_code": "121540215",
        "req_car_id": 11245,
        "req_car_type": "大众桑塔纳",
        "con_car_id": 1,
        "con_car_type": "2",
        "con_car_seat_num": null,
        "type": 1,
        "flt_no": "",
        "mile_length": 100,
        "discount_id": 23,
        "user_message": "",
        "create_at": 1504858382,
        "update_at": 1504858382,
        "title": "自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返",
        "is_use_car": 1,
        "remark": null,
        "drv_phone": null
    }
     */
    public function getPackOrderDetail(){
        $air_id = I('air_id');
        $result = $this->packLogic->get_pack_order_info($air_id,$this->user_id);
        $this->ajaxReturn($result);
    }

    /**
     * @api {POST}  /index.php?m=Api&c=PackOrder&a=payPackOrder    支付订单done 余额已好，支付宝,微信支付wxx待调   管少秋
     * @apiName     payPackOrder
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token
     * @apiParam    {Number}    air_id  订单ID
     * @apiParam    {String}    pay_way    支付方式 0微信支付 1支付宝支付 2余额支付
     * @apiParam    {Float}    [real_price]   优惠的价格
     * @apiParam    {Number}    [coupon_id]   优惠券ID 无优惠券传空进来
     */
    public function payPackOrder(){
        //判断订单是否在有效期内 有效付款时间24小时
        //判断用户
        $air_id = I('air_id');
        $pack_order = M('pack_order')->where(array('air_id' => $air_id,'status'=>0))->find();//订单详情
        $time = time();
        if(empty($pack_order)){
            $this->ajaxReturn(['status'=>-1,'msg'=>'该订单不可进行付款操作']);
        }
        if($time - $pack_order['create_at'] > 86400){//超过一天
            $this->ajaxReturn(['status'=>-1,'msg'=>'该订单已超时']);
        }
        $coupon_id = I('coupon_id');
        $pay_way = I('pay_way');//获取支付方式
        $discount_price = 0;//优惠价格
        $is_coupon =false;//优惠券是否可以用
        if(!empty($coupon_id)){
            //通过优惠券去判断需要优惠的价格
            $where = [
                'l.id' => $coupon_id,
                'l.uid' => $this->user_id,
                'l.model_type' => 0, //代表包车订单
                'c.condition' => ['lt',$pack_order['total_price']],//订单金额 要 大于优惠券的条件
                'c.status' => 1,//是否可使用
                'l.status' => 0,
                'c.use_start_time' => ['lt',$time],//是否开始 开始时间小于当前时间
                'c.use_end_time' => ['gt',$time]//是否过期    过期时间大于当前时间
            ];
            $coupon_info = M('coupon_list')->field('l.id,c.*')->alias('l')->join('__COUPON__ c','l.cid = c.id')->where($where)->find();//找出优惠券
            //echo $coupon_info;die;->fetchSql(true)
            if(empty($coupon_info)){
                $this->ajaxReturn(['status'=>-1,'msg'=>'该优惠券不满足条件']);
            }
            $is_coupon = true;//优惠券可以用
            $discount_price = $coupon_info['money'];
        }
        $real_price = $pack_order['total_price'] - $discount_price;//真实价格
        $user_info = M('users')->where('user_id',$this->user_id)->find();
        if($pay_way == 2){
            //进行付款操作----------》
            ($user_info['user_money'] - $real_price) < 0 && $this->ajaxReturn(['status'=>-1,'msg'=>'抱歉，您的账户余额不足，请使用其他方式付款']);
        }else{
            //进行支付宝微信操作

        }

        $result = payPackOrder($pack_order,$user_info,$discount_price,$pay_way,$is_coupon,$coupon_id);//订单信息,用户信息,优惠价格,支付方式,优惠券ID,优惠券是否可以使用

        $this->ajaxReturn($result);
    }

    /**
     * @api {POST}  /index.php?m=Api&c=PackOrder&a=createPackOrder      生成订单，路线订单，其他订单后台生成done 管少秋
     * @apiName     createPackOrder
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token
     * @apiParam    {Number}    line_id     路线ID
     * @apiParam    {Number}    title     路线标题
     * @apiParam    {String}    customer_name     顾客名称
     * @apiParam    {String}    customer_phone     客户的手机号
     * @apiParam    {String}    user_passport      用户护照
     * @apiParam    {String}    user_user_identity      用户的身份证
     * @apiParam    {String}    [twenty-four]      行李箱尺寸
     * @apiParam    {String}    [twenty-six]      行李箱尺寸
     * @apiParam    {String}    [twenty-eight]      行李箱尺寸
     * @apiParam    {String}    [thirty]      行李箱尺寸
     * @apiParam    {Number}    use_car_adult     成人用车人数
     * @apiParam    {Number}    use_car_children     儿童人数
     * @apiParam    {String}    work_at     服务日期
     * @apiParam    {Number}    work_address     出发地
     * @apiParam    {Number}    dest_address     目的地
     * @apiParam    {Number}    discount_id     优惠券ID
     * @apiParam    {String}    total_price     订单总价格
     *
     */
    public function createPackOrder(){

    }

}