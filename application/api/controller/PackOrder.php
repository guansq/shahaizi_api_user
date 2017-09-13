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
     */
    public function getPackOrder(){
        $type = I('type',0);
        $result = $this->packLogic->get_pack_order($type,$this->user_id);
        $this->ajaxReturn($result);
    }

    /**
     * @api {POST}   /index.php?m=Api&c=PackOrder&a=getPackOrderDetail    得到包车订单详情（未完成） 管少秋
     * @apiName     getPackOrderDetail
     * @apiGroup    PackOrder
     * @apiParam    {String}    token   token.
     */
    public function getPackOrderDetail(){

    }


}