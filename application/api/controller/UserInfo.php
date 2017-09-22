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
use think\Page;
use think\Request;

class UserInfo extends Base {
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
        $user =  $userLogic->where('user_id',$userId)->find();
        if(empty($user)){
            return $this->returnJson(4004,'要查看的用户已经不存在');
        }
        $this->checkToken();
        return $this->returnJson($userLogic->getBaseInfo($user,$this->user_id));

    }


    public function attention(Request $request){
        if($request->isPost()){
            return $this->doAttention($request);
        }
        if($request->isGet()){
            return $this->getAttentionList($request);
        }
        if($request->isDelete()){
            return $this->cancelAttention($request);
        }
        return $this->returnJson();
    }



    /**
     * @api         {GET}   index.php?m=Api&c=UserInfo&a=attention     11.进行关注 ok wxx
     * @apiName     doAttention
     * @apiGroup    UserInfo
     * @apiParam  {string}  token   token.
     * @apiParam  {number}  userId  要关注的用户的id.
     */
    private function doAttention(Request $request){
        $userId = input('userId');
        $userLogic = new UserLogic();
        $user =  $userLogic->where('user_id',$userId)->find();
        if(empty($user)){
            return $this->returnJson(4004,'要查看的用户已经不存在');
        }
        $uaLogic = new UserAttentionLogic();
        return $this->returnJson($uaLogic->addAttention($this->user_id,UserAttentionLogic::TYPE_USER, $userId));
    }

    /*
     * @api         {DELETE}   index.php?m=Api&c=UserInfo&a=attention     13.我关注的列表 todo wxx
     * @apiName     cancelAttention
     * @apiGroup    UserInfo
     * @apiParam  {string}  token   token.
     * @apiParam  {number}  userId  要关注的用户的id.
     */
    private function getAttentionList($request){
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
        $user =  $userLogic->where('user_id',$userId)->find();
        if(empty($user)){
            return $this->returnJson(4004,'要查看的用户已经不存在');
        }
        $uaLogic = new UserAttentionLogic();
        return $this->returnJson($uaLogic->removeAttention($this->user_id,UserAttentionLogic::TYPE_USER, $userId));
    }



}