<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

use app\api\logic\UserCollectLogic;
use app\api\logic\UserPraiseLogic;
use think\Model;

class GuideLogic extends Model{

    protected $table = 'ruit_article_hot_guide';


    public function getDetail($id, Model $user = null){

        $guide = $this->find($id);
        if(empty($guide)){
            return null;
        }
        !empty($user)&& $user_id = $user['user_id'];
        $guide = $guide->toArray();
        $guide['timeFmt'] = date('Y.m.d', $guide['create_at']);
        $guide['isCollect'] = UserCollectLogic::where('goods_id',$id)->where('model_type',UserCollectLogic::TYPE_STRATEGY)->where('user_id', $user_id)->count();
        $guide['isPraise'] = UserPraiseLogic::where('obj_id', $id)->where('obj_type', UserPraiseLogic::TYPE_GUIDE)->where('user_id', $user_id)->count();
        $guide['collectNum'] = UserCollectLogic::where('goods_id',$id)->where('model_type',UserCollectLogic::TYPE_STRATEGY)->count();
        $guide['praiseNum'] = UserPraiseLogic::where('obj_id', $id)->where('obj_type', UserPraiseLogic::TYPE_GUIDE)->count();
        $guide['ownerName'] = $guide['user_name']; //作者
        $owner = UsersLogic::where('user_id',$guide['user_id'])->find();
        $guide['ownerAvatar'] = empty($owner['head_pic'])?C('APP_DEFAULT_USER_AVATAR'): $owner['head_pic'];

        return $guide;
    }

}