<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */

namespace app\api\controller;

use app\api\logic\UserAttentionLogic;
use app\api\logic\UserLogic;
use app\api\logic\DynamicLogic;
use app\api\logic\StrategyLogic;
use think\Request;

class UserInfo extends Base{
    /**
     * @api         {GET}   index.php?m=Api&c=UserInfo&a=baseInfo     00.查看别人基本信息 ok wxx
     * @apiName     baseInfo
     * @apiGroup    UserInfo
     * @apiParam  {string}  token token.
     * @apiParam  {number}  userId  要查看用户的id.
     *
     * @apiSuccess {string} avatar      头像.
     * @apiSuccess {string} nickname    昵称.
     * @apiSuccess {number} sex    性别 0=保密 1=男 2=女.
     * @apiSuccess {number} level    等级.
     *
     * @apiSuccess {number} fansNum    粉丝数量 = 关注数量 (ｷ｀ﾟДﾟ´)!!.
     * @apiSuccess {number} attentionNum    关注数量.
     * @apiSuccess {number} praiseNum    被赞数量.
     * @apiSuccess {number} collectNum    被收藏数量.
     */
    public function baseInfo(){
        $userId = input('userId');
        $userLogic = new UserLogic();
        $user = $userLogic->where('user_id', $userId)->find();
        if(empty($user)){
            return $this->returnJson(4004, '要查看的用户已经不存在');
        }
        $this->checkToken();
        return $this->returnJson($userLogic->getBaseInfo($user, $this->user_id));

    }


    public function attention(Request $request){
        if($request->isPost()){
            return $this->doAttention($request);
        }
        /*if($request->isGet()){
            return $this->getAttentionList($request);
        }*/
        if($request->isDelete()){
            return $this->cancelAttention($request);
        }
        return $this->returnJson();
    }


    /**
     * @api         {POST}   index.php?m=Api&c=UserInfo&a=attention     11.进行关注 ok wxx
     * @apiName     doAttention
     * @apiGroup    UserInfo
     * @apiParam  {string}  token   token.
     * @apiParam  {number}  userId  要关注的用户的id.
     */
    private function doAttention(Request $request){
        $userId = input('userId');
        if($userId == $this->user_id){
            return $this->returnJson(4000, '关注无效');
        }
        $userLogic = new UserLogic();
        $user = $userLogic->where('user_id', $userId)->find();
        if(empty($user)){
            return $this->returnJson(4004, '要查看的用户已经不存在');
        }
        $uaLogic = new UserAttentionLogic();
        return $this->returnJson($uaLogic->addAttention($this->user_id, UserAttentionLogic::TYPE_USER, $userId));
    }

    /**
     * @api         {GET}   index.php?m=Api&c=UserInfo&a=getAttentionList     我（用户）关注的列表 todo wxx
     * @apiName     getAttentionList
     * @apiGroup    UserInfo
     * @apiParam  {string}  token   token.
     * @apiParam  {number}  user_id   用户ID.
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     */
    public function getAttentionList(){
        $user_id = I('user_id');
        $userLogic = new UserLogic();
        $result = $userLogic->getMeAttention($user_id, $this->user_id);//得到用户关注的列表
        $this->ajaxReturn($result);
    }


    /**
     * @api         {GET}   index.php?m=Api&c=UserInfo&a=getAttentionMeList     关注我（用户）的列表 todo wxx
     * @apiName     getAttentionMeList
     * @apiGroup    UserInfo
     * @apiParam  {string}  token   token.
     * @apiParam  {string}  user_id   用户ID.
     * @apiSuccess {number} page        当前页码.
     * @apiSuccess {number} totalPages  总页码数.
     * @apiSuccess {array} list         列表.
     */
    public function getAttentionMeList(){
        $user_id = I('user_id');
        $userLogic = new UserLogic();
        $result = $userLogic->getAttentionMe($user_id, $this->user_id);//得到关注该用户的列表
        $this->ajaxReturn($result);
    }

    /**
     * @api         {DELETE}   index.php?m=Api&c=UserInfo&a=attention     13.取消关注 ok wxx
     * @apiName     cancelAttention
     * @apiGroup    UserInfo
     * @apiParam  {string}  token   token.
     * @apiParam  {number}  userId  要关注的用户的id.
     */
    private function cancelAttention(Request $request){
        $userId = input('userId');
        $userLogic = new UserLogic();
        $user = $userLogic->where('user_id', $userId)->find();
        if(empty($user)){
            return $this->returnJson(4004, '要查看的用户已经不存在');
        }
        $uaLogic = new UserAttentionLogic();
        return $this->returnJson($uaLogic->removeAttention($this->user_id, UserAttentionLogic::TYPE_USER, $userId));
    }

    /**
     * @api     {GET}   index.php?m=Api&c=UserInfo&a=getOtherInfo      得到其他人动态信息，攻略，收藏的动态以及收藏的攻略
     * @apiName      getOtherInfo
     * @apiGroup     UserInfo
     * @apiParam    {Number}    userId      查看用户的ID
     * @apiParam    {String}    type      1动态|2攻略|3收藏的动态|4收藏的攻略
     */
    public function otherInfo(){
        $type = I('type');
        $user_id = I('userId');
        $userLogic = new UserLogic();
        $user = $userLogic->where('user_id', $user_id)->find();
        if(empty($user)){
            return $this->returnJson(4004, '要查看的用户已经不存在');
        }
        $dynamicLogic = new DynamicLogic();
        $strategyLogic = new StrategyLogic();
        if($type == 1){
            return $this->returnJson($dynamicLogic->getDynamicPageByUserId($user_id));
        }
        if($type == 2){
            return $this->returnJson($strategyLogic->getStrategyPageByUserId($user_id));
        }
        if($type == 3){
            return $this->returnJson($dynamicLogic->getCollectDynamicPage($user_id));
        }
        if($type == 4){
            return $this->returnJson($strategyLogic->getCollectStrategyPage($user_id));
        }
    }
}