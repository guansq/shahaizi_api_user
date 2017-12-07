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

use app\common\logic\OrderCommentLogic as BaseLogic;
use app\common\logic\SellerLogic;
/**
 *  包车订单评论
 * Class CatsLogic
 * @package common\Logic
 */
class OrderCommentLogic extends BaseLogic{

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
            'line_id' => $order['line_id'],
            'car_product_id' => $order['car_product_id'],
            'seller_id' => $order['seller_id'],
            'pack_order_score' => $reqParams['score'],//产品以及订单评分
            'drv_rank' => $reqParams['drv_rank'],//司导评分
            'line_rank' => $reqParams['line_rank'],//路线评分
            'is_anonymous' => $reqParams['isAnonymous'],
            'commemt_time' => time(),
            'deleted' => 0,
            'content' => $reqParams['content'],
            'img' => $reqParams['img'],

        ];

        if(!$this->create($commentData)){
            return resultArray(5020);
        }
        //进行评价后的推送
        $seller = SellerLogic::findByDrvId($order['seller_id']);
        if(!empty($seller)){
            $title = '客人评价订单';
            $content = '您有一条订单，客人已评价，请尽快评价';
            send_drv_msg($title,$content,$seller['seller_id']);
            //pushMessage('客人评价订单', '您有一条订单，客人已评价，请尽快评价', $seller['device_no'], $seller['seller_id'], 1);
        }
        $order->user_order_status = 1;
        if( $order->seller_order_status){
            $order->status = PackOrderLogic::STATUS_FINISH;
        }
        $order->save();
        return resultArray(2000);

    }

    /** todo
     * Author: W.W <will.wxx@qq.com>
     * Describe: 根据订单获取订单详情
     * @param $orderId
     * @Success  {Object} userComm                用户评论内容.
     * @Success  {Number} userComm.score            评分.
     * @Success  {String} userComm.content            评论文字.
     * @Success  {Array}  userComm.imgs                图片.
     * @Success  {Number} userComm.commentTime        评论时间.
     * @Success  {String} userComm.commentTimeFmt     评论时间.
     * @Success  {Object} userComm.owner              评论人信息.
     * @Success  {Object} drvComm                  司导评论内容.
     * @Success  {Number} drvComm.score            评分.
     * @Success  {String} drvComm.content            评论文字.
     * @Success  {Array}  drvComm.imgs                图片.
     * @Success  {Number} drvComm.commentTime        评论时间.
     * @Success  {String} drvComm.commentTimeFmt     评论时间.
     * @Success  {Object} sysComm                  平台评论内容.
     * @Success  {Number} sysComm.score            评分.
     * @Success  {String} sysComm.content            评论文字.
     * @Success  {Array}  sysComm.imgs                图片.
     * @Success  {Number} sysComm.commentTime        评论时间.
     * @Success  {String} sysComm.commentTimeFmt     评论时间.
     */
    public function getByOrderId($orderId){
        $fields = [
            'order_commemt_id' => 'id',
            'pack_order_score' => 'score',
            'content' => 'content',
            'img' => 'imgs',
            'commemt_time' => 'commentTime',
            'user_id' => 'comm_user_id',
            'seller_id',
            'is_anonymous' => 'isAnonymous',
        ];
        $usersFields = [
            'order_commemt_id' => 'id',
            'pack_order_score' => 'score',
            'content' => 'content',
            'img' => 'imgs',
            'commemt_time' => 'commentTime',
            'user_id' => 'comm_user_id',
            'is_anonymous' => 'isAnonymous',
            'drv_rank' => 'drv_rank',
            'line_rank' => 'line_rank',
        ];
        $userComm = $this->where('order_id', $orderId)->where('type', self::TYPE_USER)->field($usersFields)->find();
        $sysComm = $this->where('order_id', $orderId)->where('type', self::TYPE_SYSTEM)->field($fields)->find();
        $drvComm = $this->where('order_id', $orderId)->where('type', self::TYPE_SELLER)->field($fields)->find();
        if(!empty($drvComm)){
            $drvComm['head_pic'] = '';
            $drvComm['nickname'] = '';
            $seller = M('seller')->where("seller_id={$drvComm['seller_id']}")->find();
            if(!empty($seller)){
                $drvComm['head_pic'] = $seller['head_pic'];
                $drvComm['nickname'] = $seller['nickname'];
            }
        }

        $ret = [
            'userComm' => $userComm,
            'sysComm' => $sysComm,
            'drvComm' => $drvComm,
        ];
        if(empty($userComm)){
            return resultArray(4004);
        }
        foreach($ret as $k => $comm){
            $comm['imgs'] = explode('|', $comm['imgs']);
            $comm['commentTimeFmt'] = date('Y-m-d H:s', $comm['commentTime']);
        }
        $userComm['owner'] =  UserLogic::getBaseInfoById($userComm['comm_user_id'],0,$userComm['isAnonymous'])['result'];
        return resultArray(2000, '', $ret);
    }
}