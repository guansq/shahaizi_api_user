<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 16:51
 */

namespace app\api\controller;

use think\Request;

class Comment extends BaseController {
    /**
     * @api {GET}   /comment/commentInfo    获取评论内容（未完成）
     * @apiName     commentInfo
     * @apiGroup    Comment
     * @apiHeader   {String}    authorization-token     token.
     * @apiParam    {Number}    order_id                订单ID
     * @apiSuccess  {Number}    order_id                订单ID
     * @apiSuccess  {Number}    sp_id                   评论人ID
     * @apiSuccess  {String}    sp_name                 评价人的姓名
     * @apiSuccess  {Number}    dr_id                   司机ID
     * @apiSuccess  {String}    dr_name                 司机姓名
     * @apiSuccess  {String}    post_time               提交时间
     * @apiSuccess  {String}    limit_ship              发货时效几星
     * @apiSuccess  {String}    attitude                服务态度几星
     * @apiSuccess  {String}    satisfaction            满意度 几星
     * @apiSuccess  {String}    content                 评论文字
     * @apiSuccess  {Int}    status                  0=正常显示，1=不显示给司机
     */
    public function commentInfo() {
        $paramAll = $this->getReqParams([
            'order_id',
        ]);
        $rule = [
            'order_id' => ['require', 'regex' => '^[0-9]*$'],
        ];
        validateData($paramAll, $rule);
        //获取订单评论详情
        $commetInfo = model('Comment', 'logic')->getOrderCommentInfo(['order_id' => $paramAll['order_id'], 'sp_id' => $this->loginUser['id']]);

        if (!empty($commetInfo)) {
            $commetInfo['post_time'] = wztxDate($commetInfo['post_time']);
            return returnJson(2000, '成功', $commetInfo);
        }
        returnJson(4004, '未获取到订单信息');
    }

    /**
     * @api {GET}   /comment/sendCommentInfo    发送评论内容（未完成）
     * @apiName     sendCommentInfo
     * @apiGroup    Comment
     * @apiHeader   {String}    authorization-token     token.
     * @apiParam    {Number}    order_id                订单ID
     * @apiParam  {Number}    limit_ship              发货时效几星
     * @apiParam  {Number}    attitude                服务态度几星
     * @apiParam  {Number}    satisfaction            满意度 几星
     * @apiParam  {String}    content                 评论文字
     */
    public function sendCommentInfo() {
        $paramAll = $this->getReqParams([
            'order_id',
            'limit_ship',
            'attitude',
            'satisfaction',
            'content'
        ]);
        $rule = [
            'order_id' => ['require', 'regex' => '^[0-9]*$'],
            'limit_ship' => ['require', 'regex' => '[1-5]'],
            'attitude' => ['require', 'regex' => '[1-5]'],
            'satisfaction' => ['require', 'regex' => '[1-5]'],
        ];
        validateData($paramAll, $rule);
        //获取订单详情
        $orderInfo = model('TransportOrder', 'logic')->getTransportOrderInfo(['sp_id' => $this->loginUser['id'], 'id' => $paramAll['order_id']]);
        if (empty($orderInfo)) {
            returnJson('4004', '未获取到订单信息');
        }
        if ($orderInfo['status'] == 'comment') {
            returnJson('4004', '当前订单已评价过');
        }
        if (!in_array($orderInfo['status'], ['pay_success'])) {
            returnJson('4004', '订单当前状态不能评论，请支付成功后评论');
        }

        $spBaseInfo = model('SpBaseInfo', 'logic')->getPersonBaseInfo(['id' => $this->loginUser['id']]);
        $paramAll['sp_id'] = $this->loginUser['id'];
        if ($spBaseInfo['code'] == 2000) {
            $paramAll['sp_name'] = $spBaseInfo['result']['real_name'];
        } else {
            $paramAll['sp_name'] = '';
        }
        $drBaseInfo = model('DrBaseInfo', 'logic')->findInfoByUserId($orderInfo['dr_id']);
        $paramAll['dr_id'] = $orderInfo['dr_id'];
        $paramAll['dr_name'] = $drBaseInfo['real_name'];
        $paramAll['ip'] = $this->request->ip();
        $paramAll['agent'] = $_SERVER['HTTP_USER_AGENT'];
        $paramAll['post_time'] = $paramAll['create_at'] = $paramAll['update_at'] = time();
        $paramAll['status'] = 0;
        //获取pay_order_id undo
        $paramAll['pay_orderid'] = '111111111111';
        $paramAll['order_code'] =$orderInfo['order_code'];
        //没有问题存入数据库
        $changeStatus = model('TransportOrder', 'logic')->updateTransport(['id' => $paramAll['order_id']], ['status' => 'comment']);
        if ($changeStatus['code'] != '2000') {
            returnJson($changeStatus);
        }
        $ret = model('Comment', 'logic')->saveOrderComment($paramAll);
        returnJson($ret);
    }
}