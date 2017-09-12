<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 14:48
 */
namespace app\api\controller;

class PackOrder extends Base{

    /**
     * @api {GET}   /index.php?m=Api&c=getPackOrder&a=getPackOrder  得到包车订单列表（未完成）
     * @apiName     getPackOrder
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token.
     * @apiParam    {String}    type    订单状态 0未支付 1待派单 2待接单 3进行中（待开始、待确认） 4待评价 5已完成
     */
    public function getPackOrder(){
        $data = input('param.');
        dump($data);
    }

    /**
     * @api {GET}   /index.php?m=Api&c=PackOrder&a=getPackOrderDetail    得到包车订单详情（未完成）
     * @apiName     getPackOrderDetail
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token.
     */
    public function getPackOrderDetail(){

    }


}