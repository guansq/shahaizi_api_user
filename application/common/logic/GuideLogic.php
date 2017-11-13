<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/10
 * Time: 10:14
 */

namespace app\common\logic;

use think\Model;

class GuideLogic extends BaseLogic{

    protected $table = 'ruit_article_hot_guide';


    public function getDetail($id, Model $user = null){

        $guide = $this->find($id);
        if(empty($guide)){
            return null;
        }
        !empty($user)&& $user_id = $user['user_id'];
        $guide->read_num++;
        $guide->save();
        $guide = $guide->toArray();
        $guide['timeFmt'] = date('Y-m-d', $guide['publish_time']);
        $guide['isCollect'] = UserCollectLogic::where('goods_id',$id)->where('model_type',UserCollectLogic::TYPE_STRATEGY)->where('user_id', $user_id)->count();
        $guide['isPraise'] = UserPraiseLogic::where('obj_id', $id)->where('obj_type', UserPraiseLogic::TYPE_GUIDE)->where('user_id', $user_id)->count();
        $guide['collectNum'] = UserCollectLogic::where('goods_id',$id)->where('model_type',UserCollectLogic::TYPE_STRATEGY)->count();
        $guide['praiseNum'] = UserPraiseLogic::where('obj_id', $id)->where('obj_type', UserPraiseLogic::TYPE_GUIDE)->count();
        $guide['ownerName'] = $guide['user_name']; //作者
        $owner = UsersLogic::where('user_id',$guide['user_id'])->find();
        $guide['ownerAvatar'] = empty($owner['head_pic'])?C('APP_DEFAULT_USER_AVATAR'): $owner['head_pic'];

        return $guide;
    }

    /*
     * 得到热门攻略详情
     */
    public function get_hot_detail($guide_id,$user_id){
        $info = $this->find($guide_id);
        if(empty($info)){
            return ['status'=>-1,'msg'=>'没有该记录'];
        }
        $info['read_num'] = floor($info['read_num']/2);
        if($info){
            //是否点赞
            $info['isPraise'] = UserPraiseLogic::where('obj_id', $guide_id)->where('obj_type', UserPraiseLogic::TYPE_GUIDE)->where('user_id', $user_id)->count();
            //是否收藏
            $info['isCollect'] = UserCollectLogic::where('goods_id',$guide_id)->where('model_type',UserCollectLogic::TYPE_STRATEGY)->where('user_id', $user_id)->count();
        }
        $commentLogic = new CommentLogic();
        //$commentList = $commentLogic->getArticleComment($guide_id,1);
        $result = [
            'info' => $info,
            //'comment' => $commentList['list'],
        ];

        return ['status'=>1,'msg'=>'成功','result'=>$result];
    }
}