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
    const TYPE_ADMIN  = 2;
    const TYPE_DRIVER = 3;

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
            'user_id' => $user['user_id'],
            'type' => self::TYPE_USER,
            'order_id' => $order['order_id'],
            'car_product_id' => $order['car_product_id'],
            'seller_id' => $order['seller_id'],
            'pack_order_score' => $reqParams['score'],
            'is_anonymous' => $reqParams['isAnonymous'],
            'commemt_time' => time(),
            'deleted' => 0,
            'content' => $reqParams['content'],
            'img' => $reqParams['img'],

        ];

        if(!$this->create($commentData)){
            return resultArray(5020);
        }

        $order->status = PackOrderLogic::STATUS_FINISH;
        $order->save();
        return resultArray(2000);

    }

    /** todo
     * Author: W.W <will.wxx@qq.com>
     * Describe: 根据订单获取订单详情
     * @param $orderId
     * @Success  {Number} score        评分.
     * @Success  {String} content            评论文字.
     * @Success  {Array} imgs                图片.
     * @Success  {Number} commentTime        评论时间.
     * @Success  {String} commentTimeFmt     评论时间.
     * @Success  {Object} owner        评论人信息.
     */
    public function getByOrderId($orderId){
        $filed = [
            'pack_order_score' => 'score',
            'content' => 'content',
            'img' => 'imgs',
            'commemt_time' => 'commentTime',
        ];
    }

}