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


/**
 * 用户逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class PackOrderLogic extends BaseLogic{

    protected $table = 'ruit_pack_order';

    // 订单状态 0未支付 1待派单 2待接单 3进行中（待开始、待确认） 5待评价 6已完成
    const STATUS_UN_PAY     = 0;
    const STATUS_PAY        = 1;
    const STATUS_DISTRIBUTE = 2;
    const STATUS_DOING      = 3;
    const STATUS_UN_COMMENT = 5;
    const STATUS_FINISH     = 6;


    //1是接机 2是送机 3线路订单 4单次接送 5私人订制 6按天包车游
    const TYPE_MEET_AIRPORT = 1;
    const TYPE_SEND_AIRPORT = 2;
    const TYPE_LINE         = 3;
    const TYPE_SINGLE       = 4;
    const TYPE_CUSTOM       = 5;
    const TYPE_CHARTERED    = 6;


}