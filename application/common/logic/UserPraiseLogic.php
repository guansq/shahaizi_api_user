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

namespace app\common\logic;

use app\api\logic\UserLogic;
/**
 * Class CatsLogic
 * @package common\Logic
 */
class UserPraiseLogic extends BaseLogic{


    protected $table = 'ruit_user_praise';

    const TYPE_DYNAMIC         = 1;    // 动态
    const TYPE_GUIDE           = 2;    // 攻略
    const TYPE_LINE            = 3;     // 线路
    const TYPE_PACKCAR         = 4;     // 包车产品
    const TYPE_TALENT          = 5;     //达人
    const TYPE_ARTICLE         = 6;     // 文章
    const TYPE_ARTICLE_COMMENT = 7;     // 文章的评论


    const TYPE_TABLE_ARR = [
        self::TYPE_DYNAMIC => 'article_new_action',
        self::TYPE_GUIDE => 'article_hot_guide',
        self::TYPE_LINE => 'pack_line',
        self::TYPE_PACKCAR => 'pack_car_product',
        self::TYPE_TALENT => 'article_local_talent',
        self::TYPE_ARTICLE_COMMENT => 'article_comment',
    ];

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 攻略点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfGuide($id){
        return $this->where('obj_type', self::TYPE_GUIDE)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 动态点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfDynamic($id){
        return $this->where('obj_type', self::TYPE_DYNAMIC)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 线路点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfLine($id){
        return $this->where('obj_type', self::TYPE_LINE)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 包车产品点赞数
     * @param $id
     * @return int|string
     */
    public function countPraiseOfPackCar($id){
        return $this->where('obj_type', self::TYPE_PACKCAR)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 包车产品点赞数
     * @param $id
     * @return int|string
     */
    public function isPraisePackCar($id, $userId){
        return $this->where('obj_type', self::TYPE_PACKCAR)->where('obj_id', $id)->where('user_id', $userId)->count();
    }


    public function countLocalTalent($id){
        return $this->where('obj_type', self::TYPE_TALENT)->where('obj_id', $id)->count();
    }

    /**
     * Author: W.W <will.wxx@qq.com>
     * Time:
     * Describe: 是否点过赞
     * @param $id
     * @return int|string
     */
    public static function isPraised($id, $userId, $type){
        return self::where('obj_type', $type)->where('obj_id', $id)->where('user_id', $userId)->count();
    }

    public function addPraise($userId, $type, $id){

        $where = [
            'obj_id' => $id,
            'obj_type' => $type,
            'user_id' => $userId,
        ];
        if($this->where($where)->count() >= 1){
            return resultArray(4005, '您已经点过赞了。');
        }

        if($type == self::TYPE_PACKCAR){ // 包车产品不是用户发布的 没有user_id 字段
            $owner = null;
        }else{
            $owner = M(self::TYPE_TABLE_ARR[$type])->field(['user_id'])->find($id);
        }
        $ownerId = empty($owner) ? null : $owner['user_id'];

        $data = array_merge($where, ['obj_owner_id' => $ownerId]);
        if(!$this->create($data)){
            return resultArray(5020);
        }
        //记录到消息表
        if($type == self::TYPE_DYNAMIC){
            $title = '动态点赞';
            $content = '您发布的动态有新的点赞';
            if(!empty($ownerId)){
                $userLogic = new UserLogic();
                $user = $userLogic->find($ownerId);
                $title = '您发布的动态被'.$user['nickname'].'点赞';
                $content = '您发布的动态被'.$user['nickname'].'点赞';
            }
            send_msg_by_article($title,$content,$ownerId,$id,self::TYPE_DYNAMIC);
            //pushMessage($title, $content, $user['push_id'],$user['user_id'], 0);//进行点赞推送
        }
        if($type == self::TYPE_GUIDE){
            $title = '攻略点赞';
            $content = '您发布的攻略有新的点赞';
            if(!empty($ownerId)){
                $userLogic = new UserLogic();
                $user = $userLogic->find($ownerId);
                $title = '您发布的攻略被'.$user['nickname'].'点赞';
                $content = '您发布的攻略被'.$user['nickname'].'点赞';
            }
            send_msg_by_article($title,$content,$ownerId,$id,self::TYPE_GUIDE);
            //pushMessage($title, $content, $user['push_id'],$user['user_id'], 0);//进行点赞推送
        }
        return resultArray(2000);

    }
    /*
     * 增加动态的点赞数
     */
    public function setIncNewActionGood($act_id){
        M('article_new_action')->where(['act_id'=>$act_id])->setInc('good_num');
    }
}