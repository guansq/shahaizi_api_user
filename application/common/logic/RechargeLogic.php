<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

class RechargeLogic extends BaseLogic{

    protected $table = 'ruit_recharge';

    // 充值状态0:待支付 1:充值成功 2:交易关闭
    const STATUS_UNPAY = 0;
    const STATUS_PAID  = 1;
    const STATUS_CLOSE = 2;

    /**
     * Author: W.W <will.wxx@qq.com>
     * Describe:
     * @param $orderSn
     */
    public function findByOrderSn($orderSn){
        return $this->where('order_sn', $orderSn)->find();
    }

}