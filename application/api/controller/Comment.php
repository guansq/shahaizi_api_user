<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 16:51
 */

namespace app\api\controller;

use app\api\logic\OrderCommentLogic;
use app\api\logic\PackOrderLogic;
use app\api\logic\ArticleCommentLogic;
use app\common\logic\UserPraiseLogic;
use think\Request;

class Comment extends Base{

    /*
     * @api         {GET}   /api/comment/commentInfo    获取评论内容（待调试）wxx
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
    public function commentInfo(){
        $paramAll = $this->getReqParams([
            'order_id',
        ]);
        $rule = [
            'order_id' => ['require', 'regex' => '^[0-9]*$'],
        ];
        validateData($paramAll, $rule);
        //获取订单评论详情
        $commetInfo = model('Comment', 'logic')->getOrderCommentInfo([
            'order_id' => $paramAll['order_id'],
            'sp_id' => $this->loginUser['id']
        ]);

        if(!empty($commetInfo)){
            $commetInfo['post_time'] = wztxDate($commetInfo['post_time']);
            return returnJson(2000, '成功', $commetInfo);
        }
        returnJson(4004, '未获取到订单信息');
    }

    /*
     * @api  {POST}   /api/comment/sendCommentInfo    发送评论内容（待调试）wxx
     * @apiName  sendCommentInfo
     * @apiGroup Comment
     * @apiParam {String}  token  token.
     * @apiParam  {Number} order_id                订单ID
     * @apiParam  {Number} limit_ship              发货时效几星
     * @apiParam  {Number} attitude                服务态度几星
     * @apiParam  {Number} satisfaction            满意度 几星
     * @apiParam  {String} content                 评论文字
     */
    public function sendCommentInfo(){
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
        $this->validateParams($paramAll, $rule);

        //获取订单详情
        $orderInfo = model('TransportOrder', 'logic')->getTransportOrderInfo([
            'sp_id' => $this->loginUser['id'],
            'id' => $paramAll['order_id']
        ]);
        if(empty($orderInfo)){
            returnJson('4004', '未获取到订单信息');
        }
        if($orderInfo['status'] == 'comment'){
            returnJson('4004', '当前订单已评价过');
        }
        if(!in_array($orderInfo['status'], ['pay_success'])){
            returnJson('4004', '订单当前状态不能评论，请支付成功后评论');
        }

        $spBaseInfo = model('SpBaseInfo', 'logic')->getPersonBaseInfo(['id' => $this->loginUser['id']]);
        $paramAll['sp_id'] = $this->loginUser['id'];
        if($spBaseInfo['code'] == 2000){
            $paramAll['sp_name'] = $spBaseInfo['result']['real_name'];
        }else{
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
        $paramAll['order_code'] = $orderInfo['order_code'];
        //没有问题存入数据库
        $changeStatus = model('TransportOrder', 'logic')->updateTransport(['id' => $paramAll['order_id']], ['status' => 'comment']);
        if($changeStatus['code'] != '2000'){
            returnJson($changeStatus);
        }
        $ret = model('Comment', 'logic')->saveOrderComment($paramAll);
        returnJson($ret);
    }


    public function orderComment(Request $request){
        if($request->isPost()){
            return $this->postPackOrderComment($request);
        }
        if($request->isGet()){
             return $this->getPackOrderComment($request);
        }
        if($request->isDelete()){
        }
        return $this->returnJson();

    }

    /**
     * @api            {POST}   /api/comment/orderComment    10 评论包车订单 ok wxx
     * @apiDescription 评论包车订单
     * @apiName        postPackOrderComment
     * @apiGroup       Comment
     * @apiParam {String}  token  token.
     * @apiParam  {Number} orderId            订单id.
     * @apiParam  {Number={1-5}} score        评分.
     * @apiParam  {String} content            评论文字.
     * @apiParam  {String} [img]                图片,多张用“|”分割.
     * @apiParam  {Number} [isAnonymous=0]        是否匿名.
     */
    private function postPackOrderComment($request){
        $reqParams = $this->getReqParams([
            'orderId',
            'score',
            'img',
            'content',
            'isAnonymous'
        ]);
        $rule = [
            'orderId' => ['require'],
            'score' => ['require', 'regex' => '[1-5]'],
            'content' => ['require'],
        ];
        $this->validateParams($reqParams, $rule);

        $packOrderLogic = new PackOrderLogic();
        $poCommentLogic = new OrderCommentLogic();
        //获取订单详情
        $order = $packOrderLogic->find($reqParams['orderId']);
        if(empty($order)){
            return $this->returnJson(4004, '未获取到订单信息');
        }
        if($order['status'] != PackOrderLogic::STATUS_UNCOMMENT){
            return $this->returnJson(4004, '当前订单不允许评价');
        }
        if($order['user_order_status']){
            return $this->returnJson(4005, '您已经评论过');
        }
        $order['order_id'] = $reqParams['orderId'];  //
        return $this->returnJson($poCommentLogic->commentOrder($order, $reqParams, $this->user));
    }

    /**
     * @api            {GET}   /api/comment/orderComment    11 查看包车订单评论 ok wxx
     * @apiDescription 评论包车订单
     * @apiName        getPackOrderComment
     * @apiGroup       Comment
     * @apiParam {String}  token  token.
     * @apiParam  {Number} orderId            订单id.
     *
     * @apiSuccess  {Object} userComm                用户评论内容.
     * @apiSuccess  {Number} userComm.score            评分.
     * @apiSuccess  {String} userComm.content            评论文字.
     * @apiSuccess  {Array}  userComm.imgs                图片.
     * @apiSuccess  {Number} userComm.commentTime        评论时间.
     * @apiSuccess  {String} userComm.commentTimeFmt     评论时间.
     * @apiSuccess  {Object} userComm.owner              评论人信息.

     * @apiSuccess  {Object} drvComm                  司导评论内容.
     * @apiSuccess  {Number} drvComm.score            评分.
     * @apiSuccess  {String} drvComm.content            评论文字.
     * @apiSuccess  {Array}  drvComm.imgs                图片.
     * @apiSuccess  {Number} drvComm.commentTime        评论时间.
     * @apiSuccess  {String} drvComm.commentTimeFmt     评论时间.

     * @apiSuccess  {Object} sysComm                  平台评论内容.
     * @apiSuccess  {Number} sysComm.score            评分.
     * @apiSuccess  {String} sysComm.content            评论文字.
     * @apiSuccess  {Array}  sysComm.imgs                图片.
     * @apiSuccess  {Number} sysComm.commentTime        评论时间.
     * @apiSuccess  {String} sysComm.commentTimeFmt     评论时间.
     *
     */
    private function getPackOrderComment($request){
        $reqParams = $this->getReqParams([
            'orderId',
        ]);
        $rule = [
            'orderId' => ['require'],
        ];
        $this->validateParams($reqParams, $rule);
        $poCommentLogic = new OrderCommentLogic();
        return $this->returnJson($poCommentLogic->getByOrderId($reqParams['orderId']));
    }

    /**
     * @api     {POST}  /api/comment/newActionComment       最新动态评论(回复)done         管少秋
     * @apiName     newActionComment
     * @apiGroup     Comment
     * @apiParam    {String}    token   token.
     * @apiParam    {Number}    article_id   动态id.
     * @apiParam    {Number}    publish_id   发布人id.
     * @apiParam    {String}    content   评论内容.
     * @apiParam    {String}    is_anonymous   是否匿名评论.  是否匿名评价0:是；1不是
     * @apiParam    {Number}    [parent_id]   上级评论ID.
     */
    public function newActionComment(){
        $reqParams = $this->getReqParams([
            'article_id',
            'publish_id',
            'content',
            'add_time',
            'is_anonymous',
            'parent_id'//上级评论的ID
        ]);
        $rule = [
            'article_id' => ['require'],
            'publish_id' => ['require'],
            'content' => ['require'],
            'add_time' => ['require'],
            'is_anonymous' => ['require'],
        ];
        $this->validateParams($reqParams, $rule);
        $reqParams['add_time'] = time();
        $reqParams['ip_address']  = getIP();
        $reqParams['user_id']  = $this->user_id;
        $reqParams['type']  = 0;//最新动态
        $comment = new ArticleCommentLogic();
        $result = $comment->saveComment($reqParams);
        return $result;
    }

    /**
     * @api     {POST}  /api/comment/newActionTags      点赞热门动态文章done        管少秋
     * @apiName     newActionTags
     * @apiGroup    Comment
     * @apiParam    {Number}    article_id   动态id.
     */
    public function newActionTags(){
        $id = I('publish_id');
        $praiseLogic = new UserPraiseLogic();
        if(M('article_new_action')->where('act_id',$id)->count() == 0){
            return $this->returnJson(4002, '你要点赞的最新动态已经不存在。');
        }
        return $this->returnJson($praiseLogic->addPraise($this->user_id, UserPraiseLogic::TYPE_DYNAMIC, $id));
    }

    /**
     * @api     {POST}  /api/comment/doGoodByComment    对评论进行点赞
     * @apiName     doGoodByComment
     * @apiGroup    Comment
     */
    public function doGoodByComment(){

    }
}