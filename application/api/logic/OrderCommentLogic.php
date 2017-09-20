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
 *  包车订单评论
 * Class CatsLogic
 * @package common\Logic
 */
class OrderCommentLogic extends BaseLogic{

    protected $table = 'ruit_order_comment';

    /*
     * 1是普通用户 2是司导
     */

    const TYPE_USER   = 1;
    const TYPE_DRIVER = 2;

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe:
     * @param $order
     * @param $reqParams
     * @param $user
     */
    public function commentOrder($order, $reqParams, $user){
        $commentData = [
            'content' => $reqParams['content'],
            'user_id' => $user['user_id'],
            'order_id' => $order['order_id'],
            'describe_score' => $reqParams['score'],
            'seller_score' => $reqParams['score'],
            'logistics_score' => $reqParams['score'],
            'img' => $reqParams['img'],
            'is_anonymous' => $reqParams['isAnonymous'],
            'commemt_time' => time(),
            'deleted' => 0,
            'type' => self::TYPE_USER,
        ];

        if(!$this->create($commentData)){
            return resultArray(5020);
        }

        $order->status = PackOrderLogic::STATUS_FINISH;
        $order->save();
        return resultArray(2000);

    }

}