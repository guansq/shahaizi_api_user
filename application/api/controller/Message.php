<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\api\controller;
use app\api\logic\MessageLogic;

/**
 * Description of Message
 *
 * @author Administrator
 */
class Message extends Base{
//    /**
//     * 发送系统消息----测试
//     */
//    private function message(){
//        if(!request()->isPost()){
//            ajaxReturn(['status' => -1, 'msg' => '请用post请求！']);
//        }
//
//        $type = I('post.type', 1);//个体消息：0，全体消息：1
//        $admin_id = session('admin_id');
//        $users = I('post.user_id/a');//个体id
//        $category = I('post.category/d', 0); //0系统消息，1物流通知，2优惠促销，3商品提醒，4我的资产，5商城好店
//
//        $raw_data = [
//            'title' => I('post.title', ''),
//            'order_id' => I('post.order_id', 0),
//            'discription' => I('post.discription', ''), //内容
//            'goods_id' => I('post.goods_id', 0),
//            'change_type' => I('post.change_type/d', 0),
//            'money' => I('post.money/d', 0),
//            'cover' => I('post.cover', '')
//        ];
//
//        $msg_data = [
//            'admin_id' => $admin_id,
//            'category' => $category,
//            'type' => $type
//        ];
//
//        $msglogic = new \app\common\logic\MessageLogic;
//        $return = $msglogic->sendMessage($msg_data, $raw_data, $users);
//        $this->ajaxReturn($return);
//    }

    /**
     * 设置消息已读
     */
    public function message_read(){
        if(!request()->isPost()){
            $this->ajaxReturn(['status' => -1, 'msg' => '请用post请求']);
        }

        $message_id = I('post.message_id', 0);
        if(!$message_id){
            $this->ajaxReturn(['status' => -1, 'msg' => '消息id不为空']);
        }

        M('user_message')->where(['message_id' => $message_id, 'user_id' => $this->user_id])->update(['status' => 1]);
        $this->ajaxReturn(['status' => 1, 'msg' => '设置成功']);
    }

    /**
     * @api         {GET}   /index.php?m=Api&c=Message&a=getMessageList    得到消息列表 todo will
     * @apiName     getMessageList
     * @apiGroup    Message
     * @apiParam  {Number=system} [type=system]  类别.
     * @apiParam  {Number} [p=1]        页码.
     * @apiParam  {Number} [pageSize=20]   每页数据量.
     */
    public function getMessageList(){
        $msgLgc = new MessageLogic();
        return $this->returnJson($msgLgc->getSystemMsgPage());
    }


    /**
     * @api         {GET}   /index.php?m=Api&c=Message&a=getMessageDetail    得到消息详情（未完成）wxx
     * @apiName     getMessageDetail
     * @apiGroup    Message
     *
     */
    public function getMessageDetail(){

    }
}
