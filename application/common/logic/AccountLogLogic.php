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

namespace app\common\logic;

/**
 * 用户账号log逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class AccountLogLogic extends BaseLogic{

    protected $table ='ruit_account_log';

    const TYPE_RECHANGE = 1;    // 充值
    const TYPE_WITHDRAW = 2;    // 提现
    const TYPE_CONSUME = 3;    // 消费
    const TYPE_REFUND = 4;    // 退款
    const TYPE_ARR = [
        0 => '',
        self::TYPE_RECHANGE => '充值',
        self::TYPE_WITHDRAW => '提现',
        self::TYPE_CONSUME => '消费',
        self::TYPE_REFUND => '退款',
    ];

}