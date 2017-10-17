<?php
/**
 * Created by PhpStorm.
 * User: jiehua
 * Date: 15/5/2
 * Time: 下午6:21
 */

namespace payment;

/**
 * $paymentParams = [
 *  'orderSn' => $orderSn,
 *  'amount' => $reqParams['amount'],
 *  'extend' => urlencode(json_encode([
 *      'userId' => $loginUser['user_id'],
 *      'amount' => $reqParams['amount'],
 *      'orderSn' => $orderSn
 *  ]))
 * ];
 */
class PaymentBizParam{

    public $orderSn;
    public $amount;
    public $extend;

    /**
     * 析构函数
     */
    function __construct($orderSn, $amount, $extend ){
        $this->orderSn = $orderSn;
        $this->amount = $amount;
        $this->extend = $extend;
    }
}