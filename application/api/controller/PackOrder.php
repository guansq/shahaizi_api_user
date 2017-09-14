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
     * @api {POST}  /index.php?m=Api&c=PackOrder&a=payPackOrderByBalance    通过余额支付订单   管少秋
     * @apiName     payPackOrderByBalance
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token
     * @apiParam    {Number}    air_id  订单ID
     * @apiParam    {Number}    real_price   优惠后的真实价格
     */
    public function payPackOrderByBalance(){
        //判断用户
        $air_id = I('air_id');

        $order = M('order')->where(array('order_id' => $air_id,'pay_status'=>1))->find();//订单详情
        echo 'test';
    }
}